<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'empresa',
        'email',
        'telefono',
        'modalidad',
        'origen',
        'destino',
        'mercancia',
        'descripcion',
        'aceptado_privacidad',
        'ip_address',
    ];

    protected $casts = [
        'aceptado_privacidad' => 'boolean',
    ];
}