<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Comentario;
use App\Models\IdDestinatario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ComentarioController
 */
final class ComentarioControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ComentarioController::class,
            'store',
            \App\Http\Requests\ComentarioStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $id_destinatario = IdDestinatario::factory()->create();
        $tipo = fake()->randomElement(/** enum_attributes **/);
        $contenido = fake()->text();

        $response = $this->post(route('comentarios.store'), [
            'id_destinatario' => $id_destinatario->id,
            'tipo' => $tipo,
            'contenido' => $contenido,
        ]);

        $comentarios = Comentario::query()
            ->where('id_destinatario', $id_destinatario->id)
            ->where('tipo', $tipo)
            ->where('contenido', $contenido)
            ->get();
        $this->assertCount(1, $comentarios);
        $comentario = $comentarios->first();

        $response->assertRedirect(route('back'));
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $comentario = Comentario::factory()->create();

        $response = $this->delete(route('comentarios.destroy', $comentario));

        $response->assertRedirect(route('back'));

        $this->assertModelMissing($comentario);
    }
}
