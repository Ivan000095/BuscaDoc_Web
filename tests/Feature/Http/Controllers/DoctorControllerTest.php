<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DoctorController
 */
final class DoctorControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $doctors = Doctor::factory()->count(3)->create();

        $response = $this->get(route('doctors.index'));

        $response->assertOk();
        $response->assertViewIs('doctores.index');
        $response->assertViewHas('doctors', $doctors);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('doctors.create'));

        $response->assertOk();
        $response->assertViewIs('doctores.form');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DoctorController::class,
            'store',
            \App\Http\Requests\DoctorStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
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

        $response->assertRedirect(route('doctores.index'));
        $response->assertSessionHas('doctor.name', $doctor->name);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $doctor = Doctor::factory()->create();

        $response = $this->get(route('doctors.edit', $doctor));

        $response->assertOk();
        $response->assertViewIs('doctores.form');
        $response->assertViewHas('doctor', $doctor);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DoctorController::class,
            'update',
            \App\Http\Requests\DoctorUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
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

        $response->assertRedirect(route('doctores.index'));
        $response->assertSessionHas('doctor.name', $doctor->name);

        $this->assertEquals($name, $doctor->name);
        $this->assertEquals($especialidad, $doctor->especialidad);
        $this->assertEquals($cedula, $doctor->cedula);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $doctor = Doctor::factory()->create();

        $response = $this->delete(route('doctors.destroy', $doctor));

        $response->assertRedirect(route('doctores.index'));

        $this->assertModelMissing($doctor);
    }
}
