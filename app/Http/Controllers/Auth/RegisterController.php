<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\Farmacia;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Validador mejorado con reglas condicionales
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:paciente,doctor,farmacia'],
            'f_nacimiento' => ['required', 'date'],
            'foto' => ['nullable', 'image', 'max:2048'],

            'cedula' => [Rule::requiredIf($data['role'] == 'doctor')],
            'costo' => [Rule::requiredIf($data['role'] == 'doctor'), 'nullable', 'numeric'],
            'duracion_cita' => [Rule::requiredIf($data['role'] == 'doctor'), 'nullable', 'integer'],
            'horarios' => [Rule::requiredIf($data['role'] == 'doctor'), 'array'],
            'horarios.*.dia' => ['required_with:horarios', 'integer', 'between:0,6'],
            'horarios.*.inicio' => ['required_with:horarios', 'date_format:H:i'],
            'horarios.*.fin' => ['required_with:horarios', 'date_format:H:i'],

            'genero' => [Rule::requiredIf($data['role'] == 'paciente'), 'nullable', 'in:masculino,femenino,otro'],
            'tipo_sangre' => ['nullable', 'string', 'max:5'],
            'alergias' => ['nullable', 'string'],
            'padecimientos_cronicos' => ['nullable', 'string'],
            'habitos_salud' => ['nullable', 'string'],

            'nom_farmacia' => [Rule::requiredIf($data['role'] == 'farmacia')],
            'rfc' => [Rule::requiredIf($data['role'] == 'farmacia')],
            'telefono' => [Rule::requiredIf($data['role'] == 'farmacia')],
            'horario_entrada_f' => [Rule::requiredIf($data['role'] == 'farmacia')],
            'horario_salida_f' => [Rule::requiredIf($data['role'] == 'farmacia')],
        ]);
    }

    protected function create(array $data)
    {
        try {
            

            return DB::transaction(function () use ($data) {

                $rutaFoto = null;
                if (request()->hasFile('foto')) {
                    try {
                        $rutaFoto = request()->file('foto')->store('perfiles', 'public');
                    } catch (\Exception $e) {
                        throw new \Exception("Error al subir la imagen: " . $e->getMessage());
                    }
                }
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'],
                    'f_nacimiento' => $data['f_nacimiento'],
                    'foto' => $rutaFoto,
                    'latitud' => $data['latitud'] ?? 16.91173660,
                    'longitud' => $data['longitud'] ?? -92.09460000,
                ]);

                switch ($data['role']) {
                    case 'doctor':
                        $doctor = Doctor::create([
                            'user_id' => $user->id,
                            'cedula' => $data['cedula'],
                            'costo' => $data['costo'],
                            'descripcion' => $data['descripcion_doc'] ?? null,
                            'idiomas' => $data['idiomas'] ?? null,
                            'citas' => isset($data['citas']) ? true : false,
                            'duracion_cita' => $data['duracion_cita'] ?? 30, // Valor por defecto
                        ]);

                        if (isset($data['especialidades'])) {
                            $doctor->especialidades()->sync($data['especialidades']);
                        }
                        // GUARDAR LOS HORARIOS MÚLTIPLES
                        if (isset($data['horarios']) && is_array($data['horarios'])) {
                            foreach ($data['horarios'] as $item) {
                                $doctor->disponibilidades()->create([
                                    'dia_semana' => $item['dia'],
                                    'hora_inicio' => $item['inicio'],
                                    'hora_fin'    => $item['fin'],
                                ]);
                            }
                        }
                        break;

                    case 'paciente':
                    // 1. Crear el perfil de paciente
                        $paciente = \App\Models\Paciente::create([
                            'user_id' => $user->id,
                        ]);

                        // 2. Crear automáticamente su primer EXPEDIENTE (el propio)
                        \App\Models\Expediente::create([
                            'user_id' => $user->id,
                            'nombre_completo' => $user->name,
                            'fecha_nacimiento' => $user->f_nacimiento,
                            'genero' => $data['genero'],
                            'parentesco' => 'Propio', // Identificador de que es el titular
                            'tipo_sangre' => $data['tipo_sangre'] ?? null,
                            'alergias' => $data['alergias'] ?? 'Ninguna',
                            'padecimientos_cronicos' => $data['padecimientos_cronicos'] ?? 'Ninguno',
                            'habitos_salud' => $data['habitos_salud'] ?? 'No registrados',
                        ]);
                        break;

                    case 'farmacia':
                        Farmacia::create([
                            'user_id' => $user->id,
                            'nom_farmacia' => $data['nom_farmacia'],
                            'rfc' => $data['rfc'],
                            'telefono' => $data['telefono'],
                            'horario_entrada' => $data['horario_entrada_f'],
                            'horario_salida' => $data['horario_salida_f'],
                            'descripcion' => $data['descripcion'] ?? 'Sin descripción',
                        ]);
                        break;

                    default:
                        throw new \Exception("El rol seleccionado no es válido.");
                }

                return $user;
            });

        } catch (\Throwable $e) {
            Log::error('Error en registro de usuario: ' . $e->getMessage());
            if (isset($rutaFoto) && \Storage::disk('public')->exists($rutaFoto)) {
                \Storage::disk('public')->delete($rutaFoto);
            }
            throw ValidationException::withMessages([
                'email' => 'Ocurrió un error inesperado al registrar: ' . $e->getMessage(),
            ]);
        }
    }
}