<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Comentario;
use App\Models\IdRespondedor;
use App\Models\Respuesta;
use App\Models\Respuestum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\API\RespuestaController
 */
final class RespuestaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $respuesta = Respuesta::factory()->count(3)->create();

        $response = $this->get(route('respuesta.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\API\RespuestaController::class,
            'store',
            \App\Http\Requests\API\RespuestaControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
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

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $respuestum = Respuesta::factory()->create();

        $response = $this->get(route('respuesta.show', $respuestum));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\API\RespuestaController::class,
            'update',
            \App\Http\Requests\API\RespuestaControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $respuestum = Respuesta::factory()->create();
        $comentario = Comentario::factory()->create();
        $id_respondedor = IdRespondedor::factory()->create();
        $contenido = fake()->text();
        $user = User::factory()->create();

        $response = $this->put(route('respuesta.update', $respuestum), [
            'comentario_id' => $comentario->id,
            'id_respondedor' => $id_respondedor->id,
            'contenido' => $contenido,
            'user_id' => $user->id,
        ]);

        $respuestum->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($comentario->id, $respuestum->comentario_id);
        $this->assertEquals($id_respondedor->id, $respuestum->id_respondedor);
        $this->assertEquals($contenido, $respuestum->contenido);
        $this->assertEquals($user->id, $respuestum->user_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $respuestum = Respuesta::factory()->create();
        $respuestum = Respuestum::factory()->create();

        $response = $this->delete(route('respuesta.destroy', $respuestum));

        $response->assertNoContent();

        $this->assertModelMissing($respuestum);
    }
}
