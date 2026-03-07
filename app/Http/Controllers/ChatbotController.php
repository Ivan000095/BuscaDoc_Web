<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Doctor;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {

        set_time_limit(120);
        $request->validate([
            'message' => 'required|string'
        ]);

        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            return response()->json(['reply' => '⚠️ Error: No se encontró GEMINI_API_KEY en el archivo .env.'], 200);
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite-preview:generateContent?key={$apiKey}";

        $tools = [
            [
                'function_declarations' => [
                    [
                        'name' => 'buscar_doctores', 
                        'description' => 'Busca doctores en la base de datos de BuscaDoc. Úsala siempre que el usuario pida recomendaciones, pregunte por especialistas, precios o disponibilidad.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'especialidad' => [
                                    'type' => 'STRING',
                                    'description' => 'La especialidad médica solicitada (ej. ginecólogo, dentista, pediatra). Deja vacío si no especifica. NORMALIZA LA PALABRA PARA LA BASE DE DATOS. Si el paciente pide una profesión (ej. "ginecólogo", "pediatra", "cardiólogo", "dentista"), tú DEBES enviar solo la raíz de la palabra o el área médica (ej. "ginecolog", "pediatr", "cardiolog", "odontolog").'
                                ],
                                'precio_maximo' => [
                                    'type' => 'INTEGER',
                                    'description' => 'El presupuesto máximo o costo máximo que el paciente quiere pagar.'
                                ],
                                'limite' => [
                                    'type' => 'INTEGER',
                                    'description' => 'La cantidad de doctores a mostrar. Por defecto 3.'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $context = "Eres Gemini, el asistente virtual experto y amigable de BuscaDoc, un directorio médico y de farmacias. 
        Ayudas a pacientes a encontrar doctores, y a resolver dudas de la plataforma. Responde de forma breve, útil y en español de México.
        No puedes responder acerca de otras cosas.
        
        REGLAS ESTRICTAS:
        1. NUNCA inventes nombres de doctores, horarios o precios.
        2. Si un usuario pregunta por médicos, especialistas, recomendaciones o costos, DEBES usar obligatoriamente la herramienta 'buscar_doctores'.
        3. Cuando la herramienta te devuelva la información, preséntala al usuario de forma resumida, destacando el nombre (Dr. Nombre), especialidad y precio.
        4. Si la herramienta devuelve un arreglo vacío, di que por el momento no tenemos a ese especialista.
        ";

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(30)
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [['text' => $context]]
                    ],
                    'contents' => [
                        [
                            'role' => 'user', 
                            'parts' => [['text' => $userMessage]]
                        ]
                    ],
                    'tools' => $tools
                ]);

            if ($response->failed()) {
                $statuscode = $response->status();

                if ($statuscode == 503) {
                    return response()->json([
                        'reply' => 'Estoy atendiendo a muchos pacientes ahora, intenta después'
                    ]);
                }

                Log::error('Error API Gemini: ' . $response->body());
                return response()->json(['reply' => '⚠️ Error de Google API.'], 200);
            }

            $part = $response->json('candidates.0.content.parts.0');

            if (isset($part['functionCall'])) {
                $functionName = $part['functionCall']['name'];
                $args = $part['functionCall']['args'] ?? [];

                if ($functionName === 'buscar_doctores') {
                    $datosDoctores = $this->buscar_doctores_para_gemini($args);
                    $segundaRespuesta = Http::withoutVerifying()
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($url, [
                            'system_instruction' => ['parts' => [['text' => $context]]],
                            'contents' => [
                                ['role' => 'user', 'parts' => [['text' => $userMessage]]],
                                ['role' => 'model', 'parts' => [['functionCall' => $part['functionCall']]]],
                                [
                                    'role' => 'function', 
                                    'parts' => [[
                                        'functionResponse' => [
                                            'name' => 'buscar_doctores', 
                                            'response' => ['doctores' => $datosDoctores]
                                        ]
                                    ]]
                                ]
                            ]
                        ]);

                    $reply = $segundaRespuesta->json('candidates.0.content.parts.0.text');
                }
            } 
            else {
                $reply = $part['text'] ?? 'Lo siento, no pude procesar esa solicitud.';
            }

            return response()->json([
                'reply' => Str::markdown(strip_tags($reply))
            ]);

        } catch (\Exception $e) {
            Log::error('Error en Chatbot: ' . $e->getMessage());
            return response()->json(['reply' => '⚠️ Error interno: ' . $e->getMessage()], 200);
        }
    }

    private function buscar_doctores_para_gemini($args)
    {
        $query = Doctor::with(['user', 'especialidades']);

        if (isset($args['especialidad'])) {
            $especialidad = $args['especialidad'];
            $query->whereHas('especialidades', function($subQ) use ($especialidad) {
                $subQ->where('nombre', 'like', "%{$especialidad}%");
            });
        }

        if (isset($args['precio_maximo'])) {
            $query->where('costo', '<=', $args['precio_maximo']);
        }

        $doctores = $query->take($args['limite'] ?? 3)->get()->map(function($doc) {
            return [
                'nombre' => $doc->user->name,
                'especialidad' => $doc->especialidades->pluck('nombre')->join(', '),
                'precio_consulta' => $doc->costo,
                'calificacion_promedio' => round($doc->reviews->avg('calificacion') ?? 0, 1)
            ];
        });

        return $doctores->toArray();
    }
}