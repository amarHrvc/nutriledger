<?php

namespace App\Ai\Agents;

use App\Models\Patient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\UseCheapestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;


#[UseCheapestModel]
#[Temperature(0.3)]
class DietPlanAgent implements Agent, Conversational, HasStructuredOutput, HasTools
{
    use Promptable;


    public function __construct(private Patient $patient)
    {
        $patient->loadMissing('socioeconomic');
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {

        $socio = $this->patient->socioeconomic;
        $age = $this->patient->date_of_birth->age;

        $allergies           = $this->patient->allergies ?? 'None';
        $medicalNotes        = $this->patient->medical_notes ?? 'None';
        $dietaryRestrictions = $this->patient->dietary_restrictions ?? 'None';
        $foodSecurity        = $socio !== null ? ($socio->food_security_status ?? 'Unknown') : 'Unknown';
        $incomeLevel         = $socio !== null ? ($socio->income_level ?? 'Unknown') : 'Unknown';
        $activityLevel       = $socio !== null ? ($socio->physical_activity_level ?? 'Unknown') : 'Unknown';
        $smoking             = $socio !== null ? ($socio->smoking_status ?? 'Unknown') : 'Unknown';
        $alcohol             = $socio !== null ? ($socio->alcohol_consumption ?? 'Unknown') : 'Unknown';

        return <<<PROMPT
        You are a clinical nutritionist generating a 7-day meal plan for a specific patient.

        ## Patient Profile
        Age: {$age} years
        Gender: {$this->patient->gender}
        Blood Type: {$this->patient->blood_type}
        Allergies: {$allergies}
        Medical Notes: {$medicalNotes}
        Dietary Restrictions: {$dietaryRestrictions}


        ## Socioeconomic Context

        Food Security Status: {$foodSecurity}
        Income Level: {$incomeLevel}
        Physical Activity Level: {$activityLevel}
        Smoking: {$smoking}
        Alcohol Consumption: {$alcohol}

        ## Rules (NON-NEGOTIABLE)

        1. NEVER include any item from the patient's allergy list in any meal, snack, or ingredient — this includes derived products (e.g. "tree nuts" bans almonds, cashews, walnuts, almond milk, almond butter, nut oils, etc.; "shellfish" bans shrimp, crab, lobster, scallops, etc.)
        2. All meals must be realistically affordable given the patient's food security status and income level
        3. Generate exactly 7 days, one for each day Monday through Sunday
        4. Every meal field (breakfast, lunch, dinner, snack) must be a specific food description — not "balanced meal" or similar generic text
        5. Include clinical warnings in the warnings array — e.g., if the patient is food-insecure, note that affordable staples are used; if allergies were excluded, note them
        PROMPT;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'rationale' => $schema->string()->required(),

            'daily_calories' => $schema->integer()->required(),

            'nutritional_goals' => $schema->object([
                'protein_g' => $schema->integer()->required(),
                'carbs_g'   => $schema->integer()->required(),
                'fat_g'     => $schema->integer()->required(),
            ])->withoutAdditionalProperties()->required(),

            'days' => $schema->array()->items(
                $schema->object([
                    'day'       => $schema->string()->required(),
                    'breakfast' => $schema->string()->required(),
                    'lunch'     => $schema->string()->required(),
                    'dinner'    => $schema->string()->required(),
                    'snack'     => $schema->string()->required(),
                ])->withoutAdditionalProperties()
            )->required(),

            'warnings' => $schema->array()->items($schema->string())->required(),
        ];
    }
}
