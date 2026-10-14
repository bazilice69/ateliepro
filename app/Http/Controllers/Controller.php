<?php

namespace App\Http\Controllers;

use App\Support\Dinheiro;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Normaliza campos monetários do request para float, aceitando o formato
     * brasileiro ("1.500,00"). Use no início do store/update antes do validate.
     *
     * Ex.: $this->normalizarDinheiro($request, ['valor_total', 'sinal_pago']);
     */
    protected function normalizarDinheiro(Request $request, array $campos): void
    {
        $merge = [];
        foreach ($campos as $campo) {
            // Só mexe nos campos que vieram preenchidos (preserva null/ausente).
            if ($request->filled($campo)) {
                $merge[$campo] = Dinheiro::paraFloat($request->input($campo));
            }
        }
        if ($merge) {
            $request->merge($merge);
        }
    }
}
