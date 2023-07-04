<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedidaControl extends Model
{
    use HasFactory;

    protected $fillable = [
        'tokens_usados',
        'user_id',
    ];
}
