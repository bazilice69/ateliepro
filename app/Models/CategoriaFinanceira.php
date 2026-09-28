<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaFinanceira extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function lancamentos()
    {
        return $this->hasMany(LancamentoFinanceiro::class, 'categoria_id');
    }
}