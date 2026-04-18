<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // Quitamos los campos médicos de aquí, ya que van en el Expediente
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}