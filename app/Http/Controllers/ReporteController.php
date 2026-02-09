<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
public function create(Request $request)
{
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
    }

    $reportadoId = $request->query('reportado_id');
    if (!$reportadoId) {
        return redirect()->back()->with('error', 'No se especificó a quién reportar.');
    }

    // Cargar usuario con sus relaciones
    $usuario = User::with(['doctor', 'farmacia'])->find($reportadoId);
    if (!$usuario) {
        return redirect()->back()->with('error', 'Usuario no encontrado.');
    }

    // Opcional: restringir solo a doctor/farmacia
    if (!in_array($usuario->role, ['doctor', 'farmacia'])) {
        return redirect()->back()->with('error', 'Solo puedes reportar a doctores o farmacias.');
    }

    return view('reportes.user.create', compact('usuario'));
}

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
        }

        $validated = $request->validate([
            'reportado_id' => 'required|integer|exists:users,id',
            'descripcion' => 'nullable|string|max:2000',
        ]);

        if ($validated['reportado_id'] == Auth::id()) {
            return back()->withErrors(['reportado_id' => 'No puedes reportarte a ti mismo.']);
        }

        DB::transaction(function () use ($validated) {
            Reporte::create([
                'reportador_id' => Auth::id(),
                'reportado_id' => $validated['reportado_id'],
                'descripcion' => $validated['descripcion'],
                'estado' => 'pendiente',
            ]);
        });

        return redirect()->route('home')
            ->with('success', 'Tu reporte ha sido enviado. Gracias por ayudarnos a mantener la comunidad segura.');
    }

public function misReportes()
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $reportes = Reporte::with('reportado')
        ->where('reportador_id', Auth::id())
        ->latest()
        ->paginate(10);

    return view('reportes.user.mis_reportes', compact('reportes'));
}

    // Administrador: gestionar todos los reportes

    public function adminIndex()
    {
        $this->authorizeAdmin();

        $reportes = Reporte::with([
            'reportador' => function ($query) {
                $query->select('id', 'name', 'role');
            },
            'reportado' => function ($query) {
                $query->select('id', 'name', 'role');
            }
            ])
    ->latest()
    ->paginate(15);

    return view('reportes.admin.index', compact('reportes'));
    }

    public function adminShow($id)
    {
        $this->authorizeAdmin();

        $reporte = Reporte::with([
            'reportador' => fn($q) => $q->select('id', 'name', 'role'),
            'reportado' => fn($q) => $q->select('id', 'name', 'role')
                ->with(['doctor', 'farmacia'])
        ])->findOrFail($id);

        return view('reportes.admin.show', compact('reporte'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $this->authorizeAdmin();

        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,resuelto,descartado',
        ]);

        $reporte = Reporte::findOrFail($id);
        $reporte->update(['estado' => $request->estado]);

        return redirect()->route('admin.reportes.index')
            ->with('success', 'Estado actualizado correctamente.');
    }


    private function authorizeAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Acceso denegado.');
        }
    }
}