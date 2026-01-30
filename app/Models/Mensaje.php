<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_remitente',
        'id_destinatario',
        'contenido',
        'leido',
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
            'id_remitente' => 'integer',
            'id_destinatario' => 'integer',
            'leido' => 'boolean',
            'user_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function idRemitente(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function idDestinatario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
