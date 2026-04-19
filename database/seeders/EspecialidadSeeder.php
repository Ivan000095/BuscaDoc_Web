<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class EspecialidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Desactivamos llaves foráneas para poder vaciar la tabla sin errores
        Schema::disableForeignKeyConstraints();

        // 2. Vaciamos la tabla y reiniciamos el contador de IDs (Auto Increment) a 1
        Especialidad::truncate();

        // Volvemos a activar las llaves foráneas por seguridad
        Schema::enableForeignKeyConstraints();

        // 3. Arreglo asociativo con las 51 especialidades y sus descripciones
        $especialidades = [
            'Alergología' => 'Diagnóstico y tratamiento de las enfermedades alérgicas y del sistema inmunitario.',
            'Anestesiología' => 'Cuidados y alivio del dolor durante procedimientos quirúrgicos y médicos.',
            'Angiología' => 'Estudio, diagnóstico y tratamiento de las enfermedades de los vasos sanguíneos y linfáticos.',
            'Cardiología' => 'Estudio, diagnóstico y tratamiento de las enfermedades del corazón y del aparato circulatorio.',
            'Cirugía General' => 'Tratamiento quirúrgico de enfermedades del sistema digestivo, endocrino y pared abdominal.',
            'Cirugía Maxilofacial' => 'Diagnóstico y tratamiento quirúrgico de enfermedades que involucran la cara, boca y cuello.',
            'Cirugía Pediátrica' => 'Tratamiento quirúrgico de enfermedades en recién nacidos, niños y adolescentes.',
            'Cirugía Plástica' => 'Corrección quirúrgica y reparación de deformidades o defectos funcionales y estéticos.',
            'Cirugía Cardiovascular' => 'Tratamiento quirúrgico de las enfermedades del corazón y los grandes vasos.',
            'Cirugía Torácica' => 'Tratamiento quirúrgico de enfermedades de los órganos dentro del tórax (pulmones, pared torácica).',
            'Dermatología' => 'Estudio, diagnóstico y tratamiento de las enfermedades de la piel, cabello y uñas.',
            'Endocrinología' => 'Estudio y tratamiento de las glándulas que producen hormonas y de las enfermedades metabólicas.',
            'Endodoncia' => 'Rama de la odontología dedicada al tratamiento de los nervios y vasos sanguíneos de los dientes.',
            'Fisioterapia' => 'Tratamiento de enfermedades y lesiones físicas a través del movimiento, terapia manual y agentes físicos.',
            'Gastroenterología' => 'Estudio, diagnóstico y tratamiento de las enfermedades del aparato digestivo y órganos asociados.',
            'Genética Médica' => 'Diagnóstico y manejo de trastornos hereditarios y anomalías congénitas.',
            'Geriatría' => 'Prevención, diagnóstico y tratamiento de las enfermedades en las personas mayores.',
            'Ginecología' => 'Cuidado de la salud del sistema reproductor femenino y atención durante el embarazo y el parto.',
            'Hematología' => 'Estudio, diagnóstico y tratamiento de las enfermedades de la sangre y los órganos hematopoyéticos.',
            'Hepatología' => 'Estudio, diagnóstico y tratamiento de las enfermedades del hígado, la vesícula biliar y el páncreas.',
            'Infectología' => 'Diagnóstico, tratamiento y prevención de enfermedades causadas por agentes infecciosos.',
            'Inmunología' => 'Estudio del sistema inmunitario y tratamiento de las enfermedades relacionadas con su disfunción.',
            'Medicina General' => 'Atención médica integral, diagnóstico preventivo y derivación a especialistas.',
            'Medicina Interna' => 'Atención global de enfermos adultos con patologías complejas o múltiples.',
            'Medicina del Deporte' => 'Estudio de los efectos del ejercicio y tratamiento de lesiones relacionadas con el deporte.',
            'Medicina del Trabajo' => 'Prevención y tratamiento de enfermedades y lesiones relacionadas con el entorno laboral.',
            'Medicina Familiar' => 'Atención médica continua e integral para el individuo y la familia en todas las edades.',
            'Medicina Forense' => 'Aplicación de conocimientos médicos para auxiliar a la justicia en la resolución de problemas legales.',
            'Nefrología' => 'Estudio de la estructura y función de los riñones, y tratamiento de sus enfermedades.',
            'Neumología' => 'Estudio, diagnóstico y tratamiento de las enfermedades del aparato respiratorio y los pulmones.',
            'Neurocirugía' => 'Tratamiento quirúrgico de las enfermedades del sistema nervioso central y periférico.',
            'Neurología' => 'Estudio, diagnóstico y tratamiento de los trastornos del sistema nervioso.',
            'Nutrición' => 'Evaluación y tratamiento de la dieta para promover la salud y controlar enfermedades.',
            'Odontología' => 'Diagnóstico, tratamiento y prevención de las enfermedades del aparato estomatognático (dientes, encías).',
            'Odontopediatría' => 'Atención de la salud bucal de niños y adolescentes, desde el nacimiento hasta la pubertad.',
            'Oftalmología' => 'Estudio, diagnóstico y tratamiento de las enfermedades relacionadas con los ojos y la visión.',
            'Oncología' => 'Estudio, diagnóstico y tratamiento médico integral del cáncer.',
            'Ortodoncia' => 'Rama de la odontología que corrige las irregularidades en la posición de los dientes y los maxilares.',
            'Ortopedia' => 'Diagnóstico, corrección, prevención y tratamiento de trastornos del sistema musculoesquelético.',
            'Otorrinolaringología' => 'Estudio, diagnóstico y tratamiento de las enfermedades del oído, la nariz y la garganta.',
            'Pediatría' => 'Rama de la medicina enfocada en la salud y enfermedades de los niños y adolescentes.',
            'Periodoncia' => 'Rama de la odontología que trata las enfermedades de las encías y los tejidos que sostienen los dientes.',
            'Proctología' => 'Estudio, diagnóstico y tratamiento de las enfermedades del colon, recto y ano.',
            'Psicología' => 'Estudio del comportamiento humano y los procesos mentales, brindando terapia emocional.',
            'Psiquiatría' => 'Estudio, diagnóstico, tratamiento y prevención de los trastornos mentales.',
            'Quiropráctica' => 'Diagnóstico y tratamiento de los trastornos mecánicos del sistema musculoesquelético, especialmente la columna vertebral.',
            'Radiología' => 'Uso de imágenes médicas (rayos X, resonancias, etc.) para diagnosticar y tratar enfermedades.',
            'Reumatología' => 'Diagnóstico y tratamiento de enfermedades que afectan las articulaciones, músculos y huesos.',
            'Sexología' => 'Estudio y tratamiento de la sexualidad humana y de los trastornos sexuales.',
            'Traumatología' => 'Tratamiento médico y quirúrgico de lesiones traumáticas en huesos, articulaciones y músculos.',
            'Urología' => 'Estudio y tratamiento del sistema urinario y del aparato reproductor masculino.'
        ];

        $data = [];
        $now = Carbon::now();

        // 4. Armamos el arreglo mapeando la llave (nombre) y el valor (descripción)
        foreach ($especialidades as $nombre => $descripcion) {
            $data[] = [
                'nombre'      => $nombre,
                'descripcion' => $descripcion, // Asegúrate de tener este campo en tu migración
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        // 5. Insertamos todo de golpe
        Especialidad::insert($data);
    }
}