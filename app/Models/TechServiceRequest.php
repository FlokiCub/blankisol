<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'empresa',
        'email',
        'telefono',
        'servicio_interes',
        'tipo_instalacion',
        'ubicacion',
        'superficie_volumen',
        'nivel_complejidad',
        'descripcion',
        'plazo_deseado',
        'aceptado_privacidad',
        'ip_address',
    ];

    protected $casts = [
        'aceptado_privacidad' => 'boolean',
    ];
}
