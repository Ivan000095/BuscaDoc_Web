<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
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
        $response->assertViewIs('doctor.create');
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
        $user = User::factory()->create();
        $cedula = fake()->word();
        $costo = fake()->randomFloat(/** decimal_attributes **/);

        $response = $this->post(route('doctors.store'), [
            'user_id' => $user->id,
            'cedula' => $cedula,
            'costo' => $costo,
        ]);

        $doctors = Doctor::query()
            ->where('user_id', $user->id)
            ->where('cedula', $cedula)
            ->where('costo', $costo)
            ->get();
        $this->assertCount(1, $doctors);
        $doctor = $doctors->first();

        $response->assertRedirect(route('doctores.index'));
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $doctor = Doctor::factory()->create();

        $response = $this->get(route('doctors.edit', $doctor));

        $response->assertOk();
        $response->assertViewIs('doctor.edit');
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
        $cedula = fake()->word();
        $costo = fake()->randomFloat(/** decimal_attributes **/);

        $response = $this->put(route('doctors.update', $doctor), [
            'cedula' => $cedula,
            'costo' => $costo,
        ]);

        $doctor->refresh();

        $response->assertRedirect(route('doctores.index'));

        $this->assertEquals($cedula, $doctor->cedula);
        $this->assertEquals($costo, $doctor->costo);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $doctor = Doctor::factory()->create();

        $response = $this->delete(route('doctors.destroy', $doctor));

        $response->assertRedirect(route('doctors.index'));

        $this->assertModelMissing($doctor);
    }
}
