<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AcervoFoto extends Model
{
    use HasFactory, BelongsToLoja;

    protected $table = 'acervo_fotos';

    protected $fillable = ['loja_id', 'acervo_id', 'caminho', 'capa', 'ordem'];

    protected function casts(): array
    {
        return [
            'capa' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    public function acervo(): BelongsTo
    {
        return $this->belongsTo(Acervo::class);
    }

    /** URL pública da foto. */
    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->caminho);
    }
}
