<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EspecialidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de 51 especialidades reales en orden alfabético
        $especialidades = [
            'Alergología', 'Anestesiología', 'Angiología', 'Cardiología',
            'Cirugía General', 'Cirugía Maxilofacial', 'Cirugía Pediátrica',
            'Cirugía Plástica', 'Cirugía Cardiovascular', 'Cirugía Torácica',
            'Dermatología', 'Endocrinología', 'Endodoncia', 'Fisioterapia',
            'Gastroenterología', 'Genética Médica', 'Geriatría', 'Ginecología',
            'Hematología', 'Hepatología', 'Infectología', 'Inmunología',
            'Medicina General', 'Medicina Interna', 'Medicina del Deporte',
            'Medicina del Trabajo', 'Medicina Familiar', 'Medicina Forense',
            'Nefrología', 'Neumología', 'Neurocirugía', 'Neurología',
            'Nutrición', 'Odontología', 'Odontopediatría', 'Oftalmología',
            'Oncología', 'Ortodoncia', 'Ortopedia', 'Otorrinolaringología',
            'Pediatría', 'Periodoncia', 'Proctología', 'Psicología',
            'Psiquiatría', 'Quiropráctica', 'Radiología', 'Reumatología',
            'Sexología', 'Traumatología', 'Urología'
        ];

        $data = [];
        $now = Carbon::now();

        // Armamos el arreglo para meterlos todos de un solo golpe (Batch Insert)
        foreach ($especialidades as $especialidad) {
            $data[] = [
                'nombre' => $especialidad,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insertamos los 51 registros rapidísimo
        Especialidad::insert($data);
    }
}