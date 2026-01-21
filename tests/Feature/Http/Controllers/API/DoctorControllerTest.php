<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\API\DoctorController
 */
final class DoctorControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $doctors = Doctor::factory()->count(3)->create();

        $response = $this->get(route('doctors.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\API\DoctorController::class,
            'store',
            \App\Http\Requests\API\DoctorControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $name = fake()->name();
        $especialidad = fake()->word();
        $cedula = fake()->word();
        $costos = fake()->randomFloat(/** decimal_attributes **/);
        $horarioentrada = fake()->time();

        $response = $this->post(route('doctors.store'), [
            'name' => $name,
            'especialidad' => $especialidad,
            'cedula' => $cedula,
            'costos' => $costos,
            'horarioentrada' => $horarioentrada,
        ]);

        $doctors = Doctor::query()
            ->where('name', $name)
            ->where('especialidad', $especialidad)
            ->where('cedula', $cedula)
            ->where('costos', $costos)
            ->where('horarioentrada', $horarioentrada)
            ->get();
        $this->assertCount(1, $doctors);
        $doctor = $doctors->first();

        $response->assertCreated();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $doctor = Doctor::factory()->create();

        $response = $this->get(route('doctors.show', $doctor));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\API\DoctorController::class,
            'update',
            \App\Http\Requests\API\DoctorControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $doctor = Doctor::factory()->create();
        $name = fake()->name();
        $especialidad = fake()->word();
        $cedula = fake()->word();

        $response = $this->put(route('doctors.update', $doctor), [
            'name' => $name,
            'especialidad' => $especialidad,
            'cedula' => $cedula,
        ]);

        $doctor->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);

        $this->assertEquals($name, $doctor->name);
        $this->assertEquals($especialidad, $doctor->especialidad);
        $this->assertEquals($cedula, $doctor->cedula);
    }


    #[Test]
    public function destroy_deletes_and_responds_with(): void
    {
        $doctor = Doctor::factory()->create();

        $response = $this->delete(route('doctors.destroy', $doctor));

        $response->assertNoContent();

        $this->assertModelMissing($doctor);
    }
}
