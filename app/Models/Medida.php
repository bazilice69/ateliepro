<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLoja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medida extends Model
{
    use HasFactory, BelongsToLoja;

    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}