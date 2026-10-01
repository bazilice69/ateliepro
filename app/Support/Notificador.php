<?php

namespace App\Support;

use App\Models\Loja;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Envia notificações para o super_admin.
 *
 * O envio de e-mail só acontece se houver um mailer de verdade configurado
 * (MAIL_MAILER diferente de 'log'/'array'). Caso contrário, apenas registra no
 * log — assim nada quebra enquanto o e-mail não estiver configurado, e o
 * cadastro continua aparecendo no painel /admin/cadastros.
 */
class Notificador
{
    /**
     * Avisa que uma nova loja se cadastrou.
     */
    public static function novaLoja(Loja $loja): void
    {
        $nomeSistema = Setting::nomeSistema();
        $assunto = $nomeSistema . ': nova loja cadastrada — ' . $loja->nome_fantasia;
        $corpo = "Uma nova loja se cadastrou no {$nomeSistema}:\n\n"
            . "Loja: {$loja->nome_fantasia}\n"
            . "CNPJ/CPF: {$loja->cnpj_cpf}\n"
            . "Responsável/E-mail: {$loja->email_responsavel}\n"
            . "Telefone: " . ($loja->telefone ?: '-') . "\n"
            . "Data: " . now()->format('d/m/Y H:i') . "\n";

        self::enviarAoSuperAdmin($assunto, $corpo);
    }

    /**
     * Envia um e-mail simples ao(s) super_admin(s), se o mailer estiver pronto.
     */
    private static function enviarAoSuperAdmin(string $assunto, string $corpo): void
    {
        // Destinatário: e-mail de suporte configurado ou o e-mail do super_admin.
        $para = Setting::get('saas_email_suporte')
            ?: User::where('role', User::ROLE_SUPER_ADMIN)->value('email');

        if (!$para) {
            return;
        }

        // Só envia de verdade se houver um mailer configurado (não 'log'/'array').
        $mailer = config('mail.default');
        if (in_array($mailer, ['log', 'array', null], true)) {
            Log::info("[Notificador] (e-mail não configurado) {$assunto} -> {$para}");
            return;
        }

        try {
            Mail::raw($corpo, function ($message) use ($para, $assunto) {
                $message->to($para)->subject($assunto);
            });
        } catch (\Throwable $e) {
            Log::warning('[Notificador] falha ao enviar e-mail: ' . $e->getMessage());
        }
    }
}
