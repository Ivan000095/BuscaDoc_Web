<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use HasFactory;
    use SoftDeletes; // 2. Usar el trait

    protected $dates = ['deleted_at']; // 3. Opcional (versiones antiguas)

    protected $table = 'citas';

    protected $fillable = [
        'expediente_id', // Antes era paciente_id
        'doctor_id',
        'fecha',
        'hora_inicio',
        
       
        'motivo_consulta',
       
        'estado',
        'reprogramada',
    ];

    protected function casts(): array
    {
        return [
            'id'          => 'integer',
            'doctor_id'   => 'integer',
            'expediente_id' => 'integer',
            'fecha'  => 'date',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

}