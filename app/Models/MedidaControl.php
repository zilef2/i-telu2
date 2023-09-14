<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedidaControl extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tokens_usados',
        
        'pregunta',
        'respuesta_guardada',
        'subtopico_id',
        'RazonNOSubtopico',
    ];

    public function Subtopico(): BelongsTo {
        return $this->belongsTo(Subtopico::class, 'subtopico_id');
    }
    public function subtopico_nombre(): string {
        return $this->Subtopico ? $this->Subtopico->nombre : '';
    }

}
//php artisan migrate --path=/database\migrations\2023_07_01_095654_create_medida_controls_table.php
