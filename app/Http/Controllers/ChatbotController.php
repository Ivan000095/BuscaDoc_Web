<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            return response()->json(['reply' => '⚠️ Error: No se encontró GEMINI_API_KEY en el archivo .env.'], 200);
        }

        // CAMBIO CLAVE 1: Usamos gemini-pro (el modelo más estable y universal)
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}";

        // CAMBIO CLAVE 2: Le damos las instrucciones directamente en el texto
        $promptConContexto = "Eres Gemini, el asistente virtual experto y amigable de BuscaDoc, un directorio médico y de farmacias. Ayudas a pacientes a encontrar doctores, y a resolver dudas de la plataforma. Responde de forma breve, útil y en español de México.\n\nMensaje del usuario: " . $userMessage;

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(15)
                ->post($url, [
                    'contents' => [
                        [
                            'role' => 'user', 
                            'parts' => [['text' => $promptConContexto]]
                        ]
                    ]
                ]);

            if ($response->failed()) {
                Log::error('Error API Gemini: ' . $response->body());
                return response()->json(['reply' => '⚠️ Error de Google API: ' . $response->json('error.message', 'Revisa los logs.')], 200);
            }

            $reply = $response->json('candidates.0.content.parts.0.text');

            if (empty($reply)) {
                return response()->json(['reply' => 'Lo siento, no pude procesar esa solicitud.'], 200);
            }

            $htmlReply = Str::markdown(strip_tags($reply));

            return response()->json([
                'reply' => $htmlReply
            ]);

        } catch (\Exception $e) {
            Log::error('Error en Chatbot: ' . $e->getMessage());
            return response()->json(['reply' => '⚠️ Error interno del servidor: ' . $e->getMessage()], 200);
        }
    }
}