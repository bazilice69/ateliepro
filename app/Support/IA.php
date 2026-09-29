<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

/**
 * Camada de acesso ao provedor de IA (OpenAI / Anthropic / Gemini).
 *
 * A credencial e o provedor são lidos primeiro das configurações do painel
 * (tabela settings), com fallback para config/services.php (.env). Quando não
 * há chave configurada, o sistema opera em MODO DEMONSTRAÇÃO (ver
 * AssistenteService), sem chamar nenhuma API externa.
 */
class IA
{
    public static function provedor(): string
    {
        return Setting::get('ia_provedor') ?: config('services.ia.provedor', 'openai');
    }

    public static function apiKey(): ?string
    {
        return Setting::get('ia_api_key') ?: config('services.ia.api_key');
    }

    public static function modelo(): string
    {
        $modelo = Setting::get('ia_modelo') ?: config('services.ia.modelo');
        if ($modelo) {
            return $modelo;
        }

        // Modelo padrão por provedor.
        return match (self::provedor()) {
            'anthropic' => 'claude-3-5-haiku-latest',
            'gemini' => 'gemini-2.5-flash',
            default => 'gpt-4o-mini',
        };
    }

    public static function configurada(): bool
    {
        return !empty(self::apiKey());
    }

    /**
     * Nome da assistente virtual (configurável; padrão "Valentina").
     */
    public static function nomeAssistente(): string
    {
        return Setting::get('ia_nome_assistente') ?: 'Valentina';
    }

    /**
     * Envia uma conversa (system + mensagens) ao provedor e retorna o texto da
     * resposta. Lança exceção em caso de erro de API.
     *
     * @param  array<int, array{role: string, content: string}>  $mensagens
     */
    public static function conversar(string $system, array $mensagens): string
    {
        return match (self::provedor()) {
            'anthropic' => self::viaAnthropic($system, $mensagens),
            'gemini' => self::viaGemini($system, $mensagens),
            default => self::viaOpenAI($system, $mensagens),
        };
    }

    private static function viaOpenAI(string $system, array $mensagens): string
    {
        $payload = array_merge(
            [['role' => 'system', 'content' => $system]],
            $mensagens
        );

        $resp = Http::withToken(self::apiKey())
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => self::modelo(),
                'messages' => $payload,
                'temperature' => 0.5,
            ]);

        $resp->throw();

        return trim($resp->json('choices.0.message.content') ?? '');
    }

    private static function viaAnthropic(string $system, array $mensagens): string
    {
        $resp = Http::withHeaders([
            'x-api-key' => self::apiKey(),
            'anthropic-version' => '2023-06-01',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model' => self::modelo(),
            'system' => $system,
            'max_tokens' => 1024,
            'messages' => $mensagens,
        ]);

        $resp->throw();

        return trim($resp->json('content.0.text') ?? '');
    }

    private static function viaGemini(string $system, array $mensagens): string
    {
        // Gemini: concatena o system como primeira instrução do usuário.
        $contents = [];
        foreach ($mensagens as $m) {
            $contents[] = [
                'role' => $m['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $m['content']]],
            ];
        }

        $payload = [
            'system_instruction' => ['parts' => [['text' => $system]]],
            'contents' => $contents,
        ];

        // O Google muda os nomes dos modelos com frequência. Tentamos o modelo
        // configurado e, se der 404 (modelo não encontrado), caímos para
        // alternativas conhecidas — assim a Valentina não quebra a cada troca.
        $modelos = array_values(array_unique(array_filter([
            self::modelo(),
            'gemini-2.5-flash',
            'gemini-2.5-flash-lite',
            'gemini-flash-latest',
            'gemini-2.0-flash',
        ])));

        $ultimoErro = '';
        foreach ($modelos as $modelo) {
            $resp = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent?key=" . self::apiKey(),
                $payload
            );

            if ($resp->successful()) {
                return trim($resp->json('candidates.0.content.parts.0.text') ?? '');
            }

            $ultimoErro = (string) $resp->json('error.message', 'erro desconhecido');
            // Se não for "modelo não encontrado", não adianta tentar outros.
            if ($resp->status() !== 404) {
                break;
            }
        }

        throw new \RuntimeException('Gemini: ' . $ultimoErro);
    }
}
