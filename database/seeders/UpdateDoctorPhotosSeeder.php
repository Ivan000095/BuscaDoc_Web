<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UpdateDoctorPhotosSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Intentar varias rutas por si acaso
        $origenPath = public_path('image/doctores');
        
        Log::info("Iniciando Seeder. Buscando imágenes en: " . $origenPath);

        if (!File::exists($origenPath)) {
            Log::error("ERROR: La carpeta de imágenes NO existe.");
            return;
        }

        $archivos = File::files($origenPath);
        Log::info("Imágenes encontradas: " . count($archivos));

        if (count($archivos) === 0) {
            Log::error("ERROR: No hay archivos dentro de la carpeta.");
            return;
        }

        // 2. Filtro más flexible (usamos WHERE ILIKE para Postgres o LOWER para MySQL)
        $doctores = User::where('role', 'doctor')
                        ->where('email', 'ILIKE', 'doctor%') // ILIKE para Postgres (Railway)
                        ->get();
        
        // Si no encuentra con ILIKE (MySQL), intentamos normal
        if($doctores->isEmpty()){
            $doctores = User::where('role', 'doctor')
                            ->where('email', 'LIKE', 'doctor%')
                            ->get();
        }

        Log::info("Doctores encontrados para actualizar: " . $doctores->count());

        if ($doctores->isEmpty()) {
            Log::warning("No se encontraron doctores que coincidan con el criterio.");
            return;
        }

        foreach ($doctores as $index => $doctor) {
            $archivo = $archivos[$index % count($archivos)];
            $nombreArchivo = 'doc_' . $doctor->id . '_' . time() . '_' . $archivo->getFilename();
            $destinoPath = 'perfiles/' . $nombreArchivo;

            try {
                // Copiar físicamente el archivo
                Storage::disk('public')->put($destinoPath, File::get($archivo));

                // Actualizar DB
                $doctor->foto = $destinoPath;
                $doctor->save(); // Usamos save() para saltar posibles temas de $fillable

                Log::info("Doctor actualizado: {$doctor->email} con foto: {$destinoPath}");
            } catch (\Exception $e) {
                Log::error("Error actualizando al doctor {$doctor->email}: " . $e->getMessage());
            }
        }
    }
}