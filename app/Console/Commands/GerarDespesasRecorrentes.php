<?php

namespace App\Console\Commands;

use App\Services\DespesaRecorrenteService;
use Illuminate\Console\Command;

/**
 * Gera os lançamentos das despesas recorrentes de TODAS as lojas para o mês
 * atual. Idempotente (não duplica). Roda automaticamente pelo agendador.
 */
class GerarDespesasRecorrentes extends Command
{
    protected $signature = 'ateliepro:gerar-despesas-recorrentes';

    protected $description = 'Gera os lançamentos mensais das despesas recorrentes de todas as lojas';

    public function handle(DespesaRecorrenteService $service): int
    {
        $qtd = $service->gerarParaTodas();
        $this->info("Despesas recorrentes geradas: {$qtd}.");

        return self::SUCCESS;
    }
}
