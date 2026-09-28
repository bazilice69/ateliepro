<?php
namespace App\Models;
use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LancamentoFinanceiro extends Model
{
    use HasFactory, BelongsToLoja;
    protected $guarded = [];

    public function categoria()
    {
        return $this->belongsTo(CategoriaFinanceira::class, 'categoria_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}