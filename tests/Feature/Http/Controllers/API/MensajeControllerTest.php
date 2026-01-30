<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\IdDestinatario;
use App\Models\Mensaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\API\MensajeController
 */
final class MensajeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $mensajes = Mensaje::factory()->count(3)->create();

        $response = $this->get(route('mensajes.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\API\MensajeController::class,
            'store',
            \App\Http\Requests\API\MensajeControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
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

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $mensaje = Mensaje::factory()->create();

        $response = $this->get(route('mensajes.show', $mensaje));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\API\MensajeController::class,
            'update',
            \App\Http\Requests\API\MensajeControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $mensaje = Mensaje::factory()->create();
        $leido = fake()->boolean();

        $response = $this->put(route('mensajes.update', $mensaje), [
            'leido' => $leido,
        ]);

        $mensaje->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($leido, $mensaje->leido);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $mensaje = Mensaje::factory()->create();

        $response = $this->delete(route('mensajes.destroy', $mensaje));

        $response->assertNoContent();

        $this->assertModelMissing($mensaje);
    }
}
