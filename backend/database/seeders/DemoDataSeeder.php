<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $doctor = User::factory()->doctor()->withoutTwoFactor()->create([
            'name' => 'Dr. Amina Halilovic',
            'email' => 'doctor@nutribase.test',
            'password' => 'password',
        ]);

        $patients = $this->createPatients($doctor);

        // 3 patients with ~10 visits each (realistic clinical tracking)
        $this->seedVisits($patients[0], $doctor, 11);
        $this->seedVisits($patients[1], $doctor, 10);
        $this->seedVisits($patients[2], $doctor, 9);

        // 2 patients with sparse visits
        $this->seedVisits($patients[3], $doctor, 2);
        $this->seedVisits($patients[4], $doctor, 0);
    }

    /** @return Patient[] */
    private function createPatients(User $doctor): array
    {
        return [
            $this->makePatient(
                email: 'marko.petrovic@nutribase.test',
                first: 'Marko',
                last: 'Petrović',
                dob: '1985-04-12',
                gender: 'M',
                blood: 'A+',
                allergies: 'Gluten, lactose intolerance',
                notes: 'Referred for obesity management. BMI 34.2 at intake. History of hypertension managed with Ramipril 5mg.',
                address: 'Ferhadija 14', city: 'Sarajevo', postal: '71000', phone: '+387 61 123 456',
                ec_name: 'Ana Petrović', ec_phone: '+387 61 654 321',
                socio: [
                    'marital_status' => 'married',
                    'number_of_dependents' => 2,
                    'living_arrangement' => 'with_family',
                    'employment_status' => 'employed_full_time',
                    'occupation' => 'Software Engineer',
                    'income_level' => 'middle',
                    'has_health_insurance' => true,
                    'education_level' => 'bachelor',
                    'smoking_status' => 'former',
                    'alcohol_consumption' => 'occasional',
                    'physical_activity_level' => 'sedentary',
                    'has_family_support' => true,
                    'has_caregiver' => false,
                    'transportation_access' => 'own_vehicle',
                    'food_security_status' => 'secure',
                ],
            ),
            $this->makePatient(
                email: 'fatima.hadzic@nutribase.test',
                first: 'Fatima',
                last: 'Hadžić',
                dob: '1972-09-30',
                gender: 'F',
                blood: 'O+',
                allergies: 'Shellfish, tree nuts',
                notes: 'Type 2 diabetes, HbA1c 8.1% at referral. On Metformin 1000mg twice daily. Weight management and dietary counselling required.',
                address: 'Titova 22', city: 'Mostar', postal: '88000', phone: '+387 62 234 567',
                ec_name: 'Ibrahim Hadžić', ec_phone: '+387 62 765 432',
                socio: [
                    'marital_status' => 'married',
                    'number_of_dependents' => 3,
                    'living_arrangement' => 'with_family',
                    'employment_status' => 'employed_part_time',
                    'occupation' => 'Teacher',
                    'income_level' => 'middle',
                    'has_health_insurance' => true,
                    'education_level' => 'master',
                    'smoking_status' => 'never',
                    'alcohol_consumption' => 'none',
                    'physical_activity_level' => 'light',
                    'has_family_support' => true,
                    'has_caregiver' => false,
                    'transportation_access' => 'public_transport',
                    'food_security_status' => 'secure',
                ],
            ),
            $this->makePatient(
                email: 'stefan.jovanovic@nutribase.test',
                first: 'Stefan',
                last: 'Jovanović',
                dob: '1995-02-17',
                gender: 'M',
                blood: 'B-',
                allergies: null,
                notes: 'Anorexia nervosa recovery programme. Referred by psychiatry. Requires close nutritional monitoring and weekly weigh-ins.',
                address: 'Kralja Tomislava 7', city: 'Banja Luka', postal: '78000', phone: '+387 65 345 678',
                ec_name: 'Maja Jovanović', ec_phone: '+387 65 876 543',
                socio: [
                    'marital_status' => 'single',
                    'number_of_dependents' => 0,
                    'living_arrangement' => 'with_family',
                    'employment_status' => 'student',
                    'occupation' => 'University Student',
                    'income_level' => 'low',
                    'has_health_insurance' => true,
                    'education_level' => 'secondary',
                    'smoking_status' => 'never',
                    'alcohol_consumption' => 'none',
                    'physical_activity_level' => 'light',
                    'has_family_support' => true,
                    'has_caregiver' => true,
                    'transportation_access' => 'family',
                    'food_security_status' => 'at_risk',
                    'additional_notes' => 'Lives with parents during treatment. Strong family involvement in recovery plan.',
                ],
            ),
            $this->makePatient(
                email: 'lejla.kovacev@nutribase.test',
                first: 'Lejla',
                last: 'Kovačev',
                dob: '1990-06-05',
                gender: 'F',
                blood: 'AB+',
                allergies: 'Peanuts (anaphylactic)',
                notes: 'Post-bariatric surgery follow-up (sleeve gastrectomy 6 months ago). Supplementation plan active. Needs iron, B12, and vitamin D monitoring.',
                address: 'Mula Mustafe Bašeskije 9', city: 'Sarajevo', postal: '71000', phone: '+387 61 456 789',
                ec_name: 'Emir Kovačev', ec_phone: '+387 61 987 654',
                socio: [
                    'marital_status' => 'married',
                    'number_of_dependents' => 1,
                    'living_arrangement' => 'with_partner',
                    'employment_status' => 'employed_full_time',
                    'occupation' => 'Marketing Manager',
                    'income_level' => 'high',
                    'has_health_insurance' => true,
                    'education_level' => 'bachelor',
                    'smoking_status' => 'never',
                    'alcohol_consumption' => 'occasional',
                    'physical_activity_level' => 'moderate',
                    'has_family_support' => true,
                    'has_caregiver' => false,
                    'transportation_access' => 'own_vehicle',
                    'food_security_status' => 'secure',
                ],
            ),
            $this->makePatient(
                email: 'nermin.basic@nutribase.test',
                first: 'Nermin',
                last: 'Bašić',
                dob: '1960-11-23',
                gender: 'M',
                blood: 'A-',
                allergies: 'Sulphites',
                notes: 'Chronic kidney disease stage 3. Renal diet counselling — low potassium, low phosphorus, controlled protein. Referred by nephrology.',
                address: 'Obala Kulina Bana 3', city: 'Zenica', postal: '72000', phone: '+387 63 567 890',
                ec_name: 'Selma Bašić', ec_phone: '+387 63 098 765',
                socio: [
                    'marital_status' => 'widowed',
                    'number_of_dependents' => 0,
                    'living_arrangement' => 'alone',
                    'employment_status' => 'retired',
                    'occupation' => null,
                    'income_level' => 'low',
                    'has_health_insurance' => true,
                    'education_level' => 'vocational',
                    'smoking_status' => 'former',
                    'alcohol_consumption' => 'none',
                    'physical_activity_level' => 'sedentary',
                    'has_family_support' => false,
                    'has_caregiver' => true,
                    'transportation_access' => 'limited',
                    'food_security_status' => 'at_risk',
                    'additional_notes' => 'Lives alone. Community nurse visits twice weekly. Meal delivery service enrolled.',
                ],
            ),
        ];
    }

    /** @param array<string, mixed> $socio */
    private function makePatient(
        string $email,
        string $first,
        string $last,
        string $dob,
        string $gender,
        ?string $blood,
        ?string $allergies,
        string $notes,
        string $address,
        string $city,
        string $postal,
        string $phone,
        string $ec_name,
        string $ec_phone,
        array $socio,
    ): Patient {
        $user = User::factory()->patient()->withoutTwoFactor()->create([
            'name' => "{$first} {$last}",
            'email' => $email,
            'password' => 'password',
        ]);

        $patient = Patient::create([
            'user_id' => $user->id,
            'first_name' => $first,
            'last_name' => $last,
            'date_of_birth' => $dob,
            'gender' => $gender,
            'address' => $address,
            'city' => $city,
            'postal_code' => $postal,
            'phone' => $phone,
            'emergency_contact_name' => $ec_name,
            'emergency_contact_phone' => $ec_phone,
            'blood_type' => $blood,
            'allergies' => $allergies,
            'medical_notes' => $notes,
        ]);

        PatientSocioeconomic::create(array_merge(['patient_id' => $patient->id], $socio));

        return $patient;
    }

    private function seedVisits(Patient $patient, User $doctor, int $count): void
    {
        if ($count === 0) {
            return;
        }

        // Spread visits over the past 12 months, most recent first
        $baseDate = Carbon::now()->subMonths(11);

        $visitNoteTemplates = [
            'Weight reviewed. Patient reports improved dietary adherence this week. Adjusted caloric targets.',
            'Follow-up consultation. Blood work results discussed. Continuing current supplementation plan.',
            'Monthly check-in. BMI trending downward. Patient reports increased energy levels. Positive progress.',
            'Dietary recall completed. Identified high sodium intake from processed foods. Education provided.',
            'Patient reports difficulty with meal planning. Provided structured 7-day meal plan. Next review in 4 weeks.',
            'Reviewed food diary. Good compliance noted. Discussed strategies for eating out and social situations.',
            'Post-lab follow-up. HbA1c improved from previous reading. Dietary changes are showing effect.',
            'Initial nutritional assessment. Full dietary history taken. Goals established and documented.',
            'Crisis intervention session. Patient experiencing food aversion episodes. Referred back to psychiatry.',
            'Weight stable this month despite reported diet slip. Motivational interviewing conducted.',
            'Supplementation review. Iron levels normalised. Discontinuing iron supplement, maintaining B12 and D3.',
        ];

        for ($i = 0; $i < $count; $i++) {
            // Space visits roughly every 3-5 weeks
            $date = $baseDate->copy()->addWeeks($i * 4 + rand(0, 2));

            if ($date->isFuture()) {
                break;
            }

            Visit::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'date' => $date->format('Y-m-d'),
                'notes' => $visitNoteTemplates[$i % count($visitNoteTemplates)],
            ]);
        }
    }
}
