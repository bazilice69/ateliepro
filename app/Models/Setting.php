<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Configuração global chave/valor do SaaS.
 */
class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['chave', 'valor'];

    /**
     * Lê uma configuração (com cache leve).
     */
    public static function get(string $chave, ?string $default = null): ?string
    {
        return Cache::rememberForever("setting_{$chave}", function () use ($chave, $default) {
            return static::query()->where('chave', $chave)->value('valor') ?? $default;
        });
    }

    /**
     * Grava/atualiza uma configuração.
     */
    public static function set(string $chave, ?string $valor): void
    {
        static::updateOrCreate(['chave' => $chave], ['valor' => $valor]);
        Cache::forget("setting_{$chave}");
    }
}
