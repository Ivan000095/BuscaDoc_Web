<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Respuesta extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'respuestas';
    protected $fillable = [
        'comentario_id',
        'id_respondedor',
        'contenido',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'comentario_id' => 'integer',
            'id_respondedor' => 'integer',
        ];
    }

    public function comentario(): BelongsTo
    {
        return $this->belongsTo(Comentario::class, 'comentario_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_respondedor');
    }
}
