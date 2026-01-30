<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Comentario;
use App\Models\IdAutor;
use App\Models\IdDestinatario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\API\ComentarioController
 */
final class ComentarioControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $comentarios = Comentario::factory()->count(3)->create();

        $response = $this->get(route('comentarios.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\API\ComentarioController::class,
            'store',
            \App\Http\Requests\API\ComentarioControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $id_destinatario = IdDestinatario::factory()->create();
        $tipo = fake()->randomElement(/** enum_attributes **/);
        $calificacion = fake()->numberBetween(-10000, 10000);
        $contenido = fake()->text();

        $response = $this->post(route('comentarios.store'), [
            'id_destinatario' => $id_destinatario->id,
            'tipo' => $tipo,
            'calificacion' => $calificacion,
            'contenido' => $contenido,
        ]);

        $comentarios = Comentario::query()
            ->where('id_destinatario', $id_destinatario->id)
            ->where('tipo', $tipo)
            ->where('calificacion', $calificacion)
            ->where('contenido', $contenido)
            ->get();
        $this->assertCount(1, $comentarios);
        $comentario = $comentarios->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $comentario = Comentario::factory()->create();

        $response = $this->get(route('comentarios.show', $comentario));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\API\ComentarioController::class,
            'update',
            \App\Http\Requests\API\ComentarioControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $comentario = Comentario::factory()->create();
        $id_autor = IdAutor::factory()->create();
        $id_destinatario = IdDestinatario::factory()->create();
        $tipo = fake()->randomElement(/** enum_attributes **/);
        $user = User::factory()->create();

        $response = $this->put(route('comentarios.update', $comentario), [
            'id_autor' => $id_autor->id,
            'id_destinatario' => $id_destinatario->id,
            'tipo' => $tipo,
            'user_id' => $user->id,
        ]);

        $comentario->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($id_autor->id, $comentario->id_autor);
        $this->assertEquals($id_destinatario->id, $comentario->id_destinatario);
        $this->assertEquals($tipo, $comentario->tipo);
        $this->assertEquals($user->id, $comentario->user_id);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $comentario = Comentario::factory()->create();

        $response = $this->delete(route('comentarios.destroy', $comentario));

        $response->assertNoContent();

        $this->assertModelMissing($comentario);
    }
}
