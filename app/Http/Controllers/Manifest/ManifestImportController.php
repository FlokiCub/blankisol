<?php

declare(strict_types=1);

namespace App\Http\Controllers\Manifest;

use App\Enums\EquipmentStatus;
use App\Http\Controllers\Controller;
use App\Services\ManifestImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

class ManifestImportController extends Controller
{
    public function __construct(
        protected ManifestImportService $importService
    ) {}

    /**
     * Muestra la pantalla principal de subida de Manifiestos Excel/CSV.
     */
    public function index(): View
    {
        return view('manifests.import');
    }

    /**
     * Procesa el archivo subido y muestra una vista previa interactiva.
     */
    public function preview(Request $request): View|RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
            'send_whatsapp' => 'nullable|boolean',
        ]);

        try {
            $file = $request->file('file');
            $parsed = $this->importService->parseFile($file->getRealPath());

            if (empty($parsed['rows'])) {
                return redirect()->route('manifests.import.index')
                    ->with('error', 'El archivo no contiene filas de datos válidas o los encabezados no coinciden con el modelo esperado.');
            }

            // Guardar temporalmente en sesión para confirmación
            session([
                'manifest_import_rows' => $parsed['rows'],
                'manifest_import_options' => [
                    'send_whatsapp' => $request->boolean('send_whatsapp'),
                    'filename' => $file->getClientOriginalName(),
                ],
            ]);

            return view('manifests.preview', [
                'headers' => $parsed['headers'],
                'rows' => $parsed['rows'],
                'totalRows' => $parsed['total_rows'],
                'filename' => $file->getClientOriginalName(),
                'sendWhatsApp' => $request->boolean('send_whatsapp'),
            ]);
        } catch (Throwable $e) {
            return redirect()->route('manifests.import.index')
                ->with('error', 'Error al leer el archivo Excel: ' . $e->getMessage());
        }
    }

    /**
     * Ejecuta la importación definitiva de las filas previsualizadas.
     */
    public function execute(Request $request): RedirectResponse
    {
        $rows = session('manifest_import_rows');
        $options = session('manifest_import_options', []);

        if (empty($rows)) {
            return redirect()->route('manifests.import.index')
                ->with('error', 'La sesión de importación ha expirado. Por favor, sube el archivo nuevamente.');
        }

        $result = $this->importService->importRows(
            rows: $rows,
            importer: $request->user(),
            options: [
                'initial_status' => EquipmentStatus::PORT_ARRIVAL,
                'send_whatsapp' => $options['send_whatsapp'] ?? false,
            ]
        );

        // Limpiar sesión
        session()->forget(['manifest_import_rows', 'manifest_import_options']);

        $message = "✓ Importación completada: {$result['imported']} bultos creados, {$result['updated']} actualizados.";
        if ($result['failed'] > 0) {
            $message .= " ({$result['failed']} errores).";
        }

        return redirect()->route('manifests.import.index')
            ->with('success', $message)
            ->with('import_errors', $result['errors']);
    }

    /**
     * Descarga la plantilla oficial en formato .xlsx.
     */
    public function downloadTemplate(): Response
    {
        $content = $this->importService->generateTemplateExcel();
        $filename = 'modelo_manifiesto_carga_blankisol.xlsx';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
