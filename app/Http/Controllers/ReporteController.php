<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Muestra el formulario para crear un reporte (Paciente)
     */
    public function create(Request $request)
    {
        $reportadoId = $request->query('reportado_id');
        
        if (!$reportadoId) {
            return redirect()->back()->with('error', 'No se especificó a quién reportar.');
        }

        // Buscamos al usuario reportado (Doctor o Farmacia)
        $usuario = User::with(['doctor', 'farmacia'])->findOrFail($reportadoId);

        // Validación de seguridad: no reportarse a uno mismo
        if ($usuario->id === Auth::id()) {
            return redirect()->back()->with('error', 'No puedes reportarte a ti mismo.');
        }

        return view('reportes.user.create', compact('usuario'));
    }

    /**
     * Guarda el reporte en la base de datos (Paciente)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportado_id' => 'required|exists:users,id',
            'razon'        => 'required|string|min:10|max:1000',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                Reporte::create([
                    'reportador_id' => Auth::id(),
                    'reportado_id'  => $validated['reportado_id'],
                    'razon'         => $validated['razon'],
                    'estado'        => 'pendiente', // Estado inicial por defecto
                ]);
            });

            return redirect()->route('home')
                ->with('success', 'Reporte enviado con éxito. El administrador lo revisará pronto.');

} catch (\Exception $e) {
        // ESTO TE DIRÁ EL ERROR REAL (ej. campo no encontrado o error de BD)
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
    }

    /**
     * Lista de reportes realizados por el paciente autenticado
     */
    public function misReportes()
    {
        $reportes = Reporte::with('reportado')
            ->where('reportador_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('reportes.user.mis_reportes', compact('reportes'));
    }

    // =========================================================================
    // SECCIÓN ADMINISTRADOR
    // =========================================================================

    /**
     * Listado global de reportes para el Admin
     */
    public function adminIndex()
    {
        $this->authorizeAdmin();

        $reportes = Reporte::with(['reportador', 'reportado'])
            ->latest()
            ->paginate(15);

        return view('reportes.admin.index', compact('reportes'));
    }

    /**
     * Detalle de un reporte específico para el Admin
     */
    public function adminShow($id)
    {
        $this->authorizeAdmin();

        $reporte = Reporte::with(['reportador', 'reportado.doctor', 'reportado.farmacia'])
            ->findOrFail($id);

        return view('reportes.admin.show', compact('reporte'));
    }

    /**
     * Actualiza el estado del reporte (Admin)
     */
    public function adminUpdate(Request $request, $id)
    {
        $this->authorizeAdmin();

        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,resuelto,descartado',
        ]);

        $reporte = Reporte::findOrFail($id);
        
        $reporte->update([
            'estado' => $request->estado,
            'updated_at' => now() // Aseguramos la marca de tiempo de actualización
        ]);

        return redirect()->route('admin.reportes.index')
            ->with('success', "El reporte #{$id} ahora está en estado: " . str_replace('_', ' ', $request->estado));
    }

    /**
     * Middleware interno para validar rol de administrador
     */
    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para gestionar reportes.');
        }
    }
}