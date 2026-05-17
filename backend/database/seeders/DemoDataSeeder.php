<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;
use App\Models\Visit;
use App\Services\VitalSignService;
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
        $this->seedFatimaJourney($patients[1], $doctor);   // VSR2: 5 visits with vitals, positive trajectory
        $this->seedStefanJourney($patients[2], $doctor);   // VSR2: 7 visits with vitals, recovery + setback

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

    /**
     * Fatima Hadžić — Type 2 diabetes + obesity management.
     * 5 visits every 2 weeks, steady improvement across all markers.
     * Height 162 cm, starting BMI ~34 (obese), ending ~31 (still obese but significant loss).
     */
    private function seedFatimaJourney(Patient $patient, User $doctor): void
    {
        $vitals = app(VitalSignService::class);

        $visits = [
            [
                'weeksAgo' => 10,
                'notes' => 'Initial nutritional assessment. Weight 89.2 kg, BMI 34.0. Blood pressure elevated at 152/94. Caloric deficit plan initiated, low-sodium diet recommended. Metformin continued.',
                'vitals' => ['weight' => 89.20, 'height' => 162.0, 'systolic_bp' => 152, 'diastolic_bp' => 94, 'heart_rate' => 90, 'temperature' => 36.7],
            ],
            [
                'weeksAgo' => 8,
                'notes' => 'Two-week follow-up. Weight down 1.7 kg — patient reports good dietary adherence. BP improving. Encouraged to introduce 20-minute daily walks.',
                'vitals' => ['weight' => 87.50, 'height' => 162.0, 'systolic_bp' => 148, 'diastolic_bp' => 92, 'heart_rate' => 88, 'temperature' => 36.8],
            ],
            [
                'weeksAgo' => 6,
                'notes' => 'Month one review. Cumulative weight loss 3.4 kg. Patient reports improved energy. Blood pressure trending down. Meal plan adjusted for variety.',
                'vitals' => ['weight' => 85.80, 'height' => 162.0, 'systolic_bp' => 142, 'diastolic_bp' => 88, 'heart_rate' => 86, 'temperature' => 36.6],
            ],
            [
                'weeksAgo' => 4,
                'notes' => 'Excellent compliance. Weight 83.4 kg — 5.8 kg total loss. Blood pressure approaching normal range. HbA1c retest ordered. Activity level increased to moderate.',
                'vitals' => ['weight' => 83.40, 'height' => 162.0, 'systolic_bp' => 138, 'diastolic_bp' => 86, 'heart_rate' => 83, 'temperature' => 36.7],
            ],
            [
                'weeksAgo' => 2,
                'notes' => 'Latest review. Total loss 7.6 kg over 8 weeks. BP 134/84 — significant improvement. HbA1c improved from 8.1% to 7.4%. Patient highly motivated. Continuing current plan.',
                'vitals' => ['weight' => 81.60, 'height' => 162.0, 'systolic_bp' => 134, 'diastolic_bp' => 84, 'heart_rate' => 81, 'temperature' => 36.7],
            ],
        ];

        foreach ($visits as $entry) {
            $visit = Visit::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'date' => Carbon::now()->subWeeks($entry['weeksAgo'])->format('Y-m-d'),
                'notes' => $entry['notes'],
            ]);

            $vitals->record($visit, $entry['vitals']);
        }
    }

    /**
     * Stefan Jovanović — Anorexia nervosa recovery programme.
     * 7 visits every 2 weeks over 3 months. Slow but positive trajectory with a relapse at visit 4.
     * Height 178 cm, starting BMI ~13.4 (severely underweight), ending ~15.8 (still underweight but recovering).
     */
    private function seedStefanJourney(Patient $patient, User $doctor): void
    {
        $vitals = app(VitalSignService::class);

        $visits = [
            [
                'weeksAgo' => 14,
                'notes' => 'Programme intake. Critically low weight 42.5 kg, BMI 13.4. Bradycardia noted (54 bpm), temperature 35.6°C. Inpatient monitoring recommended but patient declined. Daily check-ins arranged. High-calorie oral supplementation commenced.',
                'vitals' => ['weight' => 42.50, 'height' => 178.0, 'systolic_bp' => 86, 'diastolic_bp' => 52, 'heart_rate' => 54, 'temperature' => 35.6],
            ],
            [
                'weeksAgo' => 12,
                'notes' => 'First progress check. Weight up 1.6 kg to 44.1 kg. Patient reports managing 5–6 small meals daily with family support. HR improving. Continuing structured meal plan.',
                'vitals' => ['weight' => 44.10, 'height' => 178.0, 'systolic_bp' => 89, 'diastolic_bp' => 55, 'heart_rate' => 58, 'temperature' => 36.0],
            ],
            [
                'weeksAgo' => 10,
                'notes' => 'Good progress this fortnight. Weight 46.0 kg — best gain since programme start (+1.9 kg). Patient engaged and cooperative. Adjusted supplementation to include zinc and magnesium.',
                'vitals' => ['weight' => 46.00, 'height' => 178.0, 'systolic_bp' => 92, 'diastolic_bp' => 58, 'heart_rate' => 62, 'temperature' => 36.2],
            ],
            [
                'weeksAgo' => 8,
                'notes' => 'Setback. Weight 44.3 kg — loss of 1.7 kg. Patient reports significant restriction episodes triggered by academic stress. Temperature and HR dropped. Emergency session with psychiatry arranged. Revised coping strategies provided.',
                'vitals' => ['weight' => 44.30, 'height' => 178.0, 'systolic_bp' => 87, 'diastolic_bp' => 53, 'heart_rate' => 55, 'temperature' => 35.7],
            ],
            [
                'weeksAgo' => 6,
                'notes' => 'Recovery after last week\'s setback. Weight back to 46.5 kg following adjusted psychiatric support and increased family supervision during meals. Vitals stabilising.',
                'vitals' => ['weight' => 46.50, 'height' => 178.0, 'systolic_bp' => 93, 'diastolic_bp' => 59, 'heart_rate' => 63, 'temperature' => 36.3],
            ],
            [
                'weeksAgo' => 4,
                'notes' => 'Steady progress. Weight 48.2 kg (+1.7 kg). Patient reports improved relationship with food. University exam period passed without further restriction episodes. Continuing current programme.',
                'vitals' => ['weight' => 48.20, 'height' => 178.0, 'systolic_bp' => 95, 'diastolic_bp' => 61, 'heart_rate' => 65, 'temperature' => 36.4],
            ],
            [
                'weeksAgo' => 2,
                'notes' => 'Latest review. Weight 50.2 kg — total gain of 7.7 kg since intake. BMI 15.8, still underweight but trajectory encouraging. HR and temperature normalising. Target BMI 18.5 remains the medium-term goal.',
                'vitals' => ['weight' => 50.20, 'height' => 178.0, 'systolic_bp' => 97, 'diastolic_bp' => 62, 'heart_rate' => 66, 'temperature' => 36.5],
            ],
        ];

        foreach ($visits as $entry) {
            $visit = Visit::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'date' => Carbon::now()->subWeeks($entry['weeksAgo'])->format('Y-m-d'),
                'notes' => $entry['notes'],
            ]);

            $vitals->record($visit, $entry['vitals']);
        }
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
