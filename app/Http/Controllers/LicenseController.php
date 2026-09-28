<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\License;
use Carbon\Carbon;

class LicenseController extends Controller
{
    // Função para gerar a identidade única da máquina do cliente
    public static function getMachineId()
    {
        $rawId = php_uname('n') . php_uname('s') . php_uname('m');
        return strtoupper(substr(md5($rawId), 0, 8)); // Ex: A3F8B291
    }

    // Mostra a tela de bloqueio/ativação enviando o ID da máquina
    public function index()
    {
        $machineId = self::getMachineId();
        return view('licenca.index', compact('machineId'));
    }

    // Processa e valida a chave amarrada à máquina
    public function ativar(Request $request)
    {
        $request->validate([
            'chave_licenca' => 'required|string'
        ]);

        $chave = trim($request->chave_licenca);
        $machineId = self::getMachineId();

        // O formato esperado agora é: ATELIEPRO-[ID_DA_MAQUINA]-[PLANO]-XXXX
        // Exemplo: ATELIEPRO-A3F8B291-ANUAL-CLIENTE
        $prefixoEsperado = 'ATELIEPRO-' . $machineId . '-';

        if (str_starts_with($chave, $prefixoEsperado)) {
            $partes = explode('-', $chave);
            $tipo = strtolower($partes[2] ?? 'mensal');

            if ($tipo == 'vitalicio') {
                $dataExpiracao = Carbon::now()->addYears(100);
            } elseif ($tipo == 'anual') {
                $dataExpiracao = Carbon::now()->addYear();
            } elseif ($tipo == 'semestral') {
                $dataExpiracao = Carbon::now()->addMonths(6);
            } elseif ($tipo == 'trimestral') {
                $dataExpiracao = Carbon::now()->addMonths(3);
            } else {
                $dataExpiracao = Carbon::now()->addDays(30); // Padrão mensal
            }

            // Desativa licenças antigas e salva a nova
            License::query()->update(['ativo' => false]);
            
            License::create([
                'chave_licenca' => $chave,
                'tipo_plano' => $tipo,
                'data_expiracao' => $dataExpiracao,
                'ativo' => true
            ]);

            return redirect()->route('dashboard')->with('success', 'Sistema ativado com sucesso!');
        }

        return back()->withErrors(['chave_licenca' => 'Chave inválida para esta máquina ou formato incorreto.']);
    }
}