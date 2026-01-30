<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Comentario;
use App\Models\Respuesta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RespuestaController
 */
final class RespuestaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RespuestaController::class,
            'store',
            \App\Http\Requests\RespuestaStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $comentario = Comentario::factory()->create();
        $contenido = fake()->text();

        $response = $this->post(route('respuesta.store'), [
            'comentario_id' => $comentario->id,
            'contenido' => $contenido,
        ]);

        $respuesta = Respuesta::query()
            ->where('comentario_id', $comentario->id)
            ->where('contenido', $contenido)
            ->get();
        $this->assertCount(1, $respuesta);
        $respuestum = $respuesta->first();

        $response->assertRedirect(route('back'));
    }
}
