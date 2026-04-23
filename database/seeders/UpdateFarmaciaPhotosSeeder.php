<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UpdateFarmaciaPhotosSeeder extends Seeder
{
    public function run(): void
    {
        $origenPath = public_path('images/farmacias');
        
        Log::info("Iniciando Seeder de Farmacias. Buscando en: " . $origenPath);

        if (!File::exists($origenPath)) {
            Log::error("ERROR: La carpeta {$origenPath} NO existe.");
            return;
        }

        $archivos = File::files($origenPath);
        Log::info("Imágenes de farmacias encontradas: " . count($archivos));

        if (count($archivos) === 0) {
            Log::error("ERROR: No hay archivos dentro de la carpeta.");
            return;
        }

        $farmacias = User::where('role', 'farmacia')
                        ->where('email', 'ILIKE', 'farmacia%')
                        ->get();
        
        if($farmacias->isEmpty()){
            $farmacias = User::where('role', 'farmacia')
                ->get();
        }


        Log::info("Farmacias encontradas para actualizar: " . $farmacias->count());

        if ($farmacias->isEmpty()) {
            Log::warning("No se encontraron farmacias que coincidan con el criterio.");
            return;
        }

        // 3. Iterar, copiar y actualizar
        foreach ($farmacias as $index => $farmacia) {
            // Seleccionar una foto secuencialmente
            $archivo = $archivos[$index % count($archivos)];
            
            // Generar nombre único
            $nombreArchivo = 'farma_' . $farmacia->id . '_' . time() . '_' . $archivo->getFilename();
            $destinoPath = 'perfiles/' . $nombreArchivo;

            try {
                // Copiar al storage
                Storage::disk('public')->put($destinoPath, File::get($archivo));

                // Guardar en la DB
                $farmacia->foto = $destinoPath;
                $farmacia->save(); 

                Log::info("Farmacia actualizada: {$farmacia->email} con foto: {$destinoPath}");
            } catch (\Exception $e) {
                Log::error("Error actualizando la farmacia {$farmacia->email}: " . $e->getMessage());
            }
        }
    }
}