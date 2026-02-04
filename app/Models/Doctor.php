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
    // En app/Models/Doctor.php

    public function reviews()
    {
        return $this->hasMany(Comentario::class, 'id_destinatario', 'user_id')
                    ->where('tipo', 'resena');
    }

    public function questions()
    {
        return $this->hasMany(Comentario::class, 'id_destinatario', 'user_id')
                    ->where('tipo', 'pregunta'); 
    }
    public function getPromedioCalificacionAttribute()
    {
        if ($this->reviews->isEmpty()) return 0;
        return round($this->reviews->avg('calificacion'), 1);
    }
}
