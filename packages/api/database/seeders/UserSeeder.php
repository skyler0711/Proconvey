<?php

namespace Database\Seeders;

use App\Enums\PropertyType;
use App\Enums\PropertyUserRole;
use App\Enums\UserRole;
use App\Models\Answer;
use App\Models\Conveyancer;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->hasAddress()->create([
            'first_name' => 'Admin',
            'last_name' => 'CoreBlue',
            'conveyancer_id' => Conveyancer::find(1),
            'email' => 'admin@coreblue.com',
            'role' => UserRole::Admin,
        ]);

        $conveyancer = User::factory()->hasAddress()->hasNotificationPreferences()->create([
            'first_name' => 'Conveyancer',
            'last_name' => 'CoreBlue',
            'email' => 'conveyancer@coreblue.com',
            'conveyancer_id' => Conveyancer::find(2),
            'role' => UserRole::Conveyancer,
        ]);

        $client = User::factory()
            ->hasAddress()
            ->hasAttached(Property::factory()->hasAddress()->create([
                'conveyancer_id' => $conveyancer->conveyancer->id,
                'type' => PropertyType::Sale,
                'letters_required' => 0,
                'id_check_required' => 0,
                'payment_required' => 0,
                'sof_check_required' => 0,
            ]), [
                'role' => PropertyUserRole::Owner,
                'is_primary_user' => true,
                'id_verification_completed_at' => Carbon::now(),
            ])
            ->hasAttached(Property::factory()->hasAddress()->create([
                'conveyancer_id' => $conveyancer->conveyancer->id,
                'type' => PropertyType::Purchase,
                'letters_required' => 0,
                'id_check_required' => 0,
                'payment_required' => 0,
                'sof_check_required' => 0,
            ]), [
                'role' => PropertyUserRole::Buyer,
                'is_primary_user' => true,
                'id_verification_completed_at' => Carbon::now(),
            ])
            ->hasAttached(Property::factory()->hasAddress()->create([
                'conveyancer_id' => $conveyancer->conveyancer->id,
                'type' => PropertyType::Remortgage,
                'letters_required' => 0,
                'id_check_required' => 0,
                'payment_required' => 0,
                'sof_check_required' => 0,
            ]), [
                'role' => PropertyUserRole::Remortgager,
                'is_primary_user' => true,
                'id_verification_completed_at' => Carbon::now(),
            ])
            ->create([
                'first_name' => 'Client',
                'last_name' => 'CoreBlue',
                'email' => 'client@coreblue.com',
                'role' => UserRole::Client,
            ]);

        // Seed the CoreblueClient with `ProvidedAnswers` to enable the forms on the Client Dashboard
        // This logic was taken from the `InviteNewClient` mutation:
        // - `packages/api/app/GraphQL/Mutations/Client/InviteNewClient.php`
        $client->properties->each(function (Property $property) use ($client) {
            $answers = Answer::query()
                ->with('form')
                ->whereHas('step', function ($query) use ($property) {
                    $query
                        ->whereHas('section', function ($query) use ($property) {
                            $query
                                ->whereHas('form', function ($query) use ($property) {
                                    // TODO: Filter down to the latest version of the form once that's available
                                    $query
                                        ->whereDoesntHave('conditions')
                                        ->whereNull('repeatable_answer_id')
                                        ->where('type', $property->type);
                                })
                                ->whereDoesntHave('conditions');
                        })
                        ->whereDoesntHave('conditions');
                })
                ->whereDoesntHave('conditions')
                ->get();

            $property->activeForms()->sync($answers->pluck('form.id'));

            // Get an array of form ids -> active form ids
            $activeForms = $property->activeForms()
                ->get()
                ->mapWithKeys(function ($form) {
                    return [$form->id => $form->pivot->id];
                });

            // Insert placeholder answers for each possible answer
            $property
                ->providedAnswers()
                ->createMany($answers->map(function ($answer) use ($property, $client, $activeForms) {
                    return [
                        'user_id' => $client->id,
                        'answer_id' => $answer->id,
                        'property_id' => $property->id,
                        'active_form_id' => $activeForms[$answer->form->id] ?? null,
                    ];
                }));
        });

        User::factory([
            'role' => UserRole::Client,
        ])->hasAddress()->hasNotificationPreferences()->count(17)->create();
    }
}
