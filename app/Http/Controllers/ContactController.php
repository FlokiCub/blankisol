<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Store a newly created contact/quote request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'empresa' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'modalidad' => ['required', 'string', 'in:maritimo,aereo,terrestre,aduanal,otro'],
            'origen' => ['nullable', 'string', 'max:150'],
            'destino' => ['nullable', 'string', 'max:150'],
            'mercancia' => ['nullable', 'string', 'max:200'],
            'descripcion' => ['required', 'string', 'min:5', 'max:3000'],
            'aceptado_privacidad' => ['accepted'],
        ], [
            'nombre.required' => 'Por favor indica tu nombre completo o persona de contacto.',
            'email.required' => 'El correo electrónico es requerido para responder a tu cotización.',
            'email.email' => 'Por favor introduce una dirección de correo válida.',
            'modalidad.required' => 'Selecciona el tipo de servicio o modalidad de transporte.',
            'descripcion.required' => 'Describe brevemente tu carga o requerimiento logístico.',
            'aceptado_privacidad.accepted' => 'Debes aceptar las condiciones de tratamiento de datos para continuar.',
        ]);

        $validated['ip_address'] = $request->ip();
        $validated['aceptado_privacidad'] = true;

        ContactRequest::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '¡Gracias por contactarnos! Tu solicitud de cotización ha sido registrada exitosamente.',
            ]);
        }

        return redirect()->to(url()->previous() . '#contacto')
            ->with('contact_success', '¡Gracias por comunicarte con BLANKISOL! Tu requerimiento ha sido registrado con éxito. Uno de nuestros asesores logísticos te contactará a la brevedad.');
    }
}