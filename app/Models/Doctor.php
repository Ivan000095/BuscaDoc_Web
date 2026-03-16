<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tableName',
        'user_id',
        'cedula',
        'idiomas',
        'descripcion',
        'costo',
        'especialidad',
        'horario_entrada',
        'horario_salida',
        'citas',
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
            'costo' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function especialidades()
    {
        return $this->belongsToMany(Especialidad::class, 'doctor__especialidads', 'doctor_id', 'especialidad_id');
    }

    public function reviews()
    {
        return $this->hasMany(Comentario::class, 'id_destinatario', 'user_id')
                    ->where('tipo', 'resena')
                    ->orderBy('created_at', 'desc');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
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
    public function remitente()
    {
        return $this->belongsTo(User::class, 'id_remitente');
    }
}
