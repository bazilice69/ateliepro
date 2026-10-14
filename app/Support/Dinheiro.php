<?php

namespace App\Support;

/**
 * Converte valores monetários digitados em formato brasileiro para float.
 *
 * Aceita e normaliza corretamente:
 *   "1.500,00" -> 1500.00   (ponto de milhar + vírgula decimal)
 *   "1.500"    -> 1500.00   (ponto de milhar, sem decimal)
 *   "1500,50"  -> 1500.50   (vírgula decimal)
 *   "1500.50"  -> 1500.50   (formato "en", ponto decimal)
 *   "1500"     -> 1500.00
 *   "R$ 1.500,00" -> 1500.00 (ignora símbolos)
 */
class Dinheiro
{
    public static function paraFloat($valor): float
    {
        if ($valor === null || $valor === '') {
            return 0.0;
        }
        if (is_int($valor) || is_float($valor)) {
            return (float) $valor;
        }

        // Mantém só dígitos, vírgula, ponto e sinal negativo.
        $str = preg_replace('/[^0-9,.\-]/', '', (string) $valor);
        if ($str === '' || $str === '-') {
            return 0.0;
        }

        $temVirgula = str_contains($str, ',');
        $temPonto = str_contains($str, '.');

        if ($temVirgula && $temPonto) {
            // Formato BR "1.234.567,89": ponto é milhar, vírgula é decimal.
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } elseif ($temVirgula) {
            // Só vírgula → decimal brasileiro: "1500,50".
            $str = str_replace(',', '.', $str);
        } elseif ($temPonto) {
            // Só ponto: pode ser milhar BR ("1.500") OU decimal ("1500.50").
            // Heurística: se o último grupo após o ponto tem 3 dígitos e há
            // exatamente um ponto, tratamos como MILHAR (1.500 = 1500).
            // Caso contrário (ex.: "1500.50"), é decimal.
            $partes = explode('.', $str);
            if (count($partes) > 2) {
                // Vários pontos = separadores de milhar: "1.234.567".
                $str = str_replace('.', '', $str);
            } elseif (strlen(end($partes)) === 3 && strlen($partes[0]) <= 3) {
                // Um ponto e 3 dígitos depois (ex.: "1.500") → milhar.
                $str = str_replace('.', '', $str);
            }
            // senão: mantém como decimal ("1500.50", "12.5")
        }

        return (float) $str;
    }
}
