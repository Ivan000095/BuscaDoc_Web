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
            'horario_entrada_doc' => [Rule::requiredIf($data['role'] == 'doctor')],
            'horario_salida_doc' => [Rule::requiredIf($data['role'] == 'doctor')],

            'tipo_sangre' => [Rule::requiredIf($data['role'] == 'paciente')],
            'contacto_emergencia' => [Rule::requiredIf($data['role'] == 'paciente')],

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
                            'horario_entrada' => $data['horario_entrada_doc'],
                            'horario_salida' => $data['horario_salida_doc'],
                            'idiomas' => $data['idiomas'] ?? 'Español',
                            'descripcion' => $data['descripcion_doc'] ?? 'Sin descripción',
                            'citas' => !empty($data['citas']) && $data['citas'] !== '0',
                        ]);

                        if (isset($data['especialidades'])) {
                            $doctor->especialidades()->sync($data['especialidades']);
                        }
                        break;

                    case 'paciente':
                        Paciente::create([
                            'user_id' => $user->id,
                            'tipo_sangre' => $data['tipo_sangre'],
                            'contacto_emergencia' => $data['contacto_emergencia'],
                            'alergias' => $data['alergias'] ?? 'Sin alergias.',
                            'cirugias' => $data['cirugias'] ?? 'No ha tenido cirugías',
                            'padecimientos' => $data['padecimientos'] ?? 'No hay ningún padecimiento.',
                            'habitos' => $data['habitos'] ?? 'No hay hábitos registrados.',
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