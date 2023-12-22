<?php

namespace Tests\Graphql\Auth;

use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Conveyancer;
use App\Models\Property;
use App\Models\User;
use Tests\TestCase;

// two conveyancers
// three properties (two for main conveyancer, same number, different streets)
// one conveyancerUser
// test can search for owned properties
// test search returns both for number only, then one for adding street

class GlobalSearchTest extends TestCase
{
    private $conveyancingFirm;

    private $altConveyancingFirm;

    private $conveyancer;

    private $authorised_conveyancer;

    private $client;

    private $property_1;

    private $property_2;

    protected function setUp(): void
    {
        parent::setUp();

        $conveyancingFirm = Conveyancer::factory()->create([
            'id' => 1,
        ]);

        $altConveyancingFirm = Conveyancer::factory()->create([
            'id' => 2,
        ]);

        $this->conveyancer = User::factory()->create([
            'role' => UserRole::Conveyancer,
            'conveyancer_id' => 1,
        ]);

        $this->authorised_conveyancer = $this->conveyancer->conveyancer_id;
    }

    private function globalSearchQuery($user, $query = '')
    {
        $instance = $this;

        if ($user) {
            $instance = $this->actingAs($user);
        }

        return $instance
            ->graphQL(
                /** @lang GraphQL */
                '
                query testGlobalQuery ($filters: GlobalSearchInput!) {
                    globalSearch(input: $filters) {
                        id
                        type
                        line_1
                        display_text
                        users {
                          id
                          first_name
                          last_name
                        }
                    }
                  } 
            ', [
                    'filters' => [
                        'search' => $query,
                    ],

                ]
            );
    }

    public function test_user_cannot_search_for_properties_from_other_conveyancers()
    {
        $property_1 = Property::factory()->has(Address::factory())->create([
            'conveyancer_id' => $this->conveyancer->conveyancer_id,
        ]);

        $property_2 = Property::factory()->has(Address::factory())->create([
            'conveyancer_id' => 2,
        ]);

        $this->assertDatabaseCount('properties', 2);

        $this->globalSearchQuery($this->conveyancer, '')->assertJsonCount(1);
    }

    public function test_return_all_properties_for_user()
    {
        $user_1 = User::factory()->has(Property::factory()->hasAddress()->count(10)->state([
            'conveyancer_id' => $this->conveyancer->conveyancer_id,
        ]))->create([
            'conveyancer_id' => $this->conveyancer->conveyancer_id,
        ]);

        User::factory()->has(Property::factory()->hasAddress()->count(10)->state([
            'conveyancer_id' => $this->conveyancer->conveyancer_id,
        ]))->create([
            'conveyancer_id' => $this->conveyancer->conveyancer_id,
        ]);

        $this->assertDatabaseCount('properties', 20);
        $this->globalSearchQuery($this->conveyancer, "$user_1->first_name $user_1->last_name")->assertJsonCount(10, 'data.globalSearch');
    }

    public function test_return_all_users_of_property()
    {
        Property::factory()->hasAddress()->has(User::factory()->hasAddress()->count(10)->state([
            'role' => UserRole::Client,
        ]), 'users')->state([
            'conveyancer_id' => $this->conveyancer->conveyancer_id,
        ])->create();

        $property = Property::factory()->hasAddress()->has(User::factory()->hasAddress()->count(10)->state([
            'role' => UserRole::Client,
        ]), 'users')->state([
            'conveyancer_id' => $this->conveyancer->conveyancer_id,
        ])->create();

        $this->assertDatabaseCount('users', 21);
        $this->globalSearchQuery($this->conveyancer, $property->line_1)->assertJsonCount(10, 'data.globalSearch.0.users');
    }

    public function test_address_specificity()
    {
        $property_1 = Property::factory()->has(Address::factory()->state([
            'line_1' => '123 Test Street',
        ]))->state([
            'conveyancer_id' => $this->conveyancer->conveyancer_id,
        ])->create();

        $property_2 = Property::factory()->has(Address::factory()->state([
            'line_1' => '123 Test Road',
        ]))->state([
            'conveyancer_id' => $this->conveyancer->conveyancer_id,
        ])->create();

        $property_3 = Property::factory()->has(Address::factory()->state([
            'line_1' => '123 Fake Street',
        ]))->state([
            'conveyancer_id' => $this->conveyancer->conveyancer_id,
        ])->create();

        $this->globalSearchQuery($this->conveyancer, '123')->assertJsonCount(3, 'data.globalSearch');
        $this->globalSearchQuery($this->conveyancer, '123 test')->assertJsonCount(2, 'data.globalSearch');
        $this->globalSearchQuery($this->conveyancer, '123 Fake')->assertJsonCount(1, 'data.globalSearch');
    }
}
