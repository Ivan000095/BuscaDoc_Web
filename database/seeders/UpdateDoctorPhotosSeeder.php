<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UpdateDoctorPhotosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definir la ruta de origen donde pondrás tus fotos manualmente
        $origenPath = public_path('images/doctores');

        // Validar que la carpeta exista
        if (!File::exists($origenPath)) {
            $this->command->error("La carpeta {$origenPath} no existe. Por favor, créala y agrega algunas imágenes.");
            return;
        }

        // Obtener todos los archivos de esa carpeta
        $archivos = File::files($origenPath);

        if (empty($archivos)) {
            $this->command->error("No hay imágenes dentro de {$origenPath}.");
            return;
        }

        // 2. Filtrar a los doctores cuyo email empiece con "doctor"
        $doctores = User::where('role', 'doctor')
                        ->where('email', 'LIKE', 'doctor%')
                        ->get();

        if ($doctores->isEmpty()) {
            $this->command->info("No se encontraron doctores con un email que empiece con 'doctor'.");
            return;
        }

        $this->command->info("Iniciando actualización para " . $doctores->count() . " doctores...");

        // 3. Iterar y actualizar
        foreach ($doctores as $index => $doctor) {
            // Seleccionar una foto secuencialmente (si hay menos fotos que doctores, se repiten usando módulo)
            $archivo = $archivos[$index % count($archivos)];
            
            // Generar un nombre único para evitar colisiones en el storage
            $nombreArchivo = 'doc_' . $doctor->id . '_' . $archivo->getFilename();
            $destinoPath = 'perfiles/' . $nombreArchivo;

            // Copiar el archivo al disco de Storage (storage/app/public/perfiles)
            Storage::disk('public')->put($destinoPath, File::get($archivo));

            // Actualizar la base de datos
            $doctor->update([
                'foto' => $destinoPath
            ]);

            $this->command->line("✅ Actualizado: {$doctor->email} -> {$nombreArchivo}");
        }

        $this->command->info("¡Todas las fotos fueron actualizadas con éxito!");
    }
}