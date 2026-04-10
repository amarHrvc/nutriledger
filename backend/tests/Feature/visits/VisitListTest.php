<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test unauthenticated user cannot access list
     */
    public function test_guest_cannot_list_visits(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->getJson("/api/patients/{$patient->id}/visits");

        $response->assertUnauthorized();
    }

    /**
     * Test doctor can list any patient's visits
     */
    public function test_doctor_can_list_any_patient_visits(): void
    {
        $doctor = User::factory()->create(['role' => 'doktor']);
        $patient = Patient::factory()->create();
        $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

        $response = $this->actingAs($doctor)
            ->getJson("/api/patients/{$patient->id}/visits");

        $response->assertOk()
            ->assertJsonStructure(['message', 'status', 'data' => [0 => ['type', 'id', 'attributes', 'relationships']], 'meta', 'links'])
            ->assertJsonCount(1, 'data');
    }

    /**
     * Test admin can list any patient's visits
     */
    public function test_admin_can_list_any_patient_visits(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $patient = Patient::factory()->create();
        $visit = Visit::factory()->create(['patient_id' => $patient->id]);

        $response = $this->actingAs($admin)
            ->getJson("/api/patients/{$patient->id}/visits");

        $response->assertOk()
            ->assertJsonStructure(['message', 'status', 'data' => [0 => ['type', 'id', 'attributes', 'relationships']], 'meta', 'links'])
            ->assertJsonCount(1, 'data');
    }

    /**
     * Test patient can list own visits
     */
    public function test_patient_can_list_own_visits(): void
    {
        $doctor = User::factory()->create(['role' => 'doktor']);
        $patientUser = User::factory()->create(['role' => 'pacijent']);
        $patient = Patient::factory()->create(['user_id' => $patientUser->id]);
        $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

        $response = $this->actingAs($patientUser)
            ->getJson("/api/patients/{$patient->id}/visits");

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /**
     * Test patient cannot list another patient's visits
     */
    public function test_patient_cannot_list_other_patient_visits(): void
    {
        $patientUser1 = User::factory()->create(['role' => 'pacijent']);
        $patientUser2 = User::factory()->create(['role' => 'pacijent']);
        $patient1 = Patient::factory()->create(['user_id' => $patientUser1->id]);
        $patient2 = Patient::factory()->create(['user_id' => $patientUser2->id]);

        Visit::factory()->create(['patient_id' => $patient2->id]);

        $response = $this->actingAs($patientUser1)
            ->getJson("/api/patients/{$patient2->id}/visits");

        $response->assertForbidden();
    }

    /**
     * Test visits are returned in date descending order
     */
    public function test_visits_returned_in_date_descending_order(): void
    {
        $doctor = User::factory()->create(['role' => 'doktor']);
        $patient = Patient::factory()->create();

        Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'date' => '2026-04-05']);
        Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'date' => '2026-04-10']);
        Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'date' => '2026-04-01']);

        $response = $this->actingAs($doctor)
            ->getJson("/api/patients/{$patient->id}/visits");

        $response->assertOk();
        $dates = collect($response->json('data'))->pluck('attributes.date')->all();
        $this->assertEquals(['2026-04-10', '2026-04-05', '2026-04-01'], $dates);
    }

    /**
     * Test pagination meta is present
     */
    public function test_list_response_includes_pagination_meta(): void
    {
        $doctor = User::factory()->create(['role' => 'doktor']);
        $patient = Patient::factory()->create();
        Visit::factory(15)->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

        $response = $this->actingAs($doctor)
            ->getJson("/api/patients/{$patient->id}/visits?per_page=10");

        $response->assertOk()
            ->assertJsonStructure(['message', 'status', 'data', 'meta' => ['total', 'per_page', 'current_page', 'last_page'], 'links']);
    }

    /**
     * Test doctorName is included in visit attributes
     */
    public function test_visit_includes_doctor_name_in_attributes(): void
    {
        $doctor = User::factory()->create(['role' => 'doktor', 'name' => 'Dr. Smith']);
        $patient = Patient::factory()->create();
        Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

        $response = $this->actingAs($doctor)
            ->getJson("/api/patients/{$patient->id}/visits");

        $response->assertOk()
            ->assertJsonPath('data.0.attributes.doctorName', 'Dr. Smith');
    }

    /**
     * Test empty list for patient with no visits
     */
    public function test_patient_with_no_visits_returns_empty_array(): void
    {
        $doctor = User::factory()->create(['role' => 'doktor']);
        $patient = Patient::factory()->create();

        $response = $this->actingAs($doctor)
            ->getJson("/api/patients/{$patient->id}/visits");

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
