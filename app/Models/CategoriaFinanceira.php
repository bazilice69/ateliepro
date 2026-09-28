<?php
namespace App\Models;
use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaFinanceira extends Model
{
    use HasFactory, BelongsToLoja;
    protected $guarded = [];

    public function lancamentos()
    {
        return $this->hasMany(LancamentoFinanceiro::class, 'categoria_id');
    }
}