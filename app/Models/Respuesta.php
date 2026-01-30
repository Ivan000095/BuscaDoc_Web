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
    protected $fillable = [
        'comentario_id',
        'id_respondedor',
        'contenido',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'comentario_id' => 'integer',
            'id_respondedor' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function comentario(): BelongsTo
    {
        return $this->belongsTo(Comentario::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function idRespondedor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
