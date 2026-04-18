<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk('local');
        $files = $disk->files('public/backups');
        $backups = [];

        foreach ($files as $file) {
            if (substr($file, -4) == '.zip' && $disk->exists($file)) {
                $backups[] = [
                    'file_path' => $file,
                    'file_name' => str_replace('public/backups/', '', $file),
                    'file_size' => $this->humanFilesize($disk->size($file)),
                    'last_modified' => Carbon::createFromTimestamp($disk->lastModified($file))->format('Y-m-d H:i:s'),
                ];
            }
        }

        // Ordenar del más reciente al más antiguo
        $backups = array_reverse($backups);

        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        try {
            set_time_limit(300);
            
            Artisan::call('backup:run', ['--only-db' => true]);

            return redirect()->back()->with('success', 'Respaldo creado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function download($file_name)
    {
        $file = 'public/backups/' . $file_name;
        $disk = Storage::disk('local');

        if ($disk->exists($file)) {
            return $disk->download($file);
        }

        return redirect()->back()->with('error', 'El archivo no existe.');
    }

    public function destroy($file_name)
    {
        $file = 'public/backups/' . $file_name;
        $disk = Storage::disk('local');

        if ($disk->exists($file)) {
            $disk->delete($file);
            return redirect()->back()->with('success', 'Respaldo eliminado correctamente.');
        }

        return redirect()->back()->with('error', 'El archivo no existe.');
    }

    // Función auxiliar para mostrar el peso en MB
    private function humanFilesize($bytes, $decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}