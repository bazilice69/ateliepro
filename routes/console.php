<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Gera as despesas recorrentes de todas as lojas, todo dia 1º às 06:00.
// (Idempotente: só lança o que ainda não foi lançado no mês.)
Schedule::command('ateliepro:gerar-despesas-recorrentes')
    ->monthlyOn(1, '06:00');
