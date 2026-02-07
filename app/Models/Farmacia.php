<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Farmacia extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'nom_farmacia',
        'rfc',
        'telefono',
        'descripcion',
        'horario_entrada',
        'horario_salida',
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
            'user_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
    public function reviews()
    {
        return $this->hasMany(Comentario::class, 'id_destinatario', 'user_id')
                    ->where('tipo', 'resena')
                    ->orderBy('created_at', 'desc');
    }

    public function questions()
    {
        return $this->hasMany(Comentario::class, 'id_destinatario', 'user_id')
                    ->where('tipo', 'pregunta')
                    ->orderBy('created_at', 'desc');
    }
    public function getPromedioCalificacionAttribute()
    {
        if ($this->reviews->isEmpty()) return 0;
        return round($this->reviews->avg('calificacion'), 1);
    }
}
