<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\IdDestinatario;
use App\Models\Mensaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\MensajeController
 */
final class MensajeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $mensajes = Mensaje::factory()->count(3)->create();

        $response = $this->get(route('mensajes.index'));

        $response->assertOk();
        $response->assertViewIs('mensajes.index');
        $response->assertViewHas('mensajes', $mensajes);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\MensajeController::class,
            'store',
            \App\Http\Requests\MensajeStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $id_destinatario = IdDestinatario::factory()->create();
        $contenido = fake()->text();

        $response = $this->post(route('mensajes.store'), [
            'id_destinatario' => $id_destinatario->id,
            'contenido' => $contenido,
        ]);

        $mensajes = Mensaje::query()
            ->where('id_destinatario', $id_destinatario->id)
            ->where('contenido', $contenido)
            ->get();
        $this->assertCount(1, $mensajes);
        $mensaje = $mensajes->first();

        $response->assertRedirect(route('mensajes.index'));
    }
}
