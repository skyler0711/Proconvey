<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Conveyancer;
use App\Models\User;
use App\Services\CompaniesHouseService\CompaniesHouseService;
use Mockery\MockInterface;
use Stripe\Service\CustomerService;
use Stripe\StripeClient;
use Tests\TestCase;

class UpdateConveyancerBusinessDetailsTest extends TestCase
{
    private $stripeClient;

    private $customerService;

    private $companiesHouseService;

    private $companiesHouseServiceMock;

    private $user;

    private $altUser;

    private $conveyancer;

    private $name;

    private $company_number;

    private $sra_clc_number;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(StripeClient::class);

        $this->stripeClient = $this->mock(StripeClient::class);

        $this->customerService = $this->mock(CustomerService::class, function (MockInterface $mock) {
            $mock->shouldReceive('update');
        });

        $this->stripeClient->customers = $this->customerService;

        $this->mock(CompaniesHouseService::class, function (MockInterface $mock) {
            $mock->shouldReceive('validateCompany')->andReturn(true);
        });

        $this->name = fake()->company();
        $this->company_number = fake()->randomNumber(8);
        $this->sra_clc_number = fake()->randomNumber(8);

        $this->conveyancer = Conveyancer::factory()->hasAddress()->create([
            'name' => $this->name,
            'company_number' => $this->company_number,
            'sra_clc_number' => $this->sra_clc_number,
        ]);

        $this->user = User::factory()->create([
            'role' => UserRole::Conveyancer,
            'conveyancer_id' => $this->conveyancer->id,
        ]);
    }

    private function updateConveyancerDetailsMutation($user, $args)
    {
        $instance = $this;

        if ($user) {
            $instance = $this->actingAs($user);
        }

        return $instance
            ->graphQL(
                /** @lang GraphQL */
                '
                mutation updateConveyancerDetails($input: UpdateConveyancerDetailsInput!) {
                    updateConveyancerDetails(input: $input) {
                      name
                      company_number
                      sra_clc_number
                      trading_name
                      vat_number
                      website
                      location
                      telephone_number
                      email_address
                    }
                  }
            ',
                [
                    'input' => $args,
                ],
            );
    }

    /**
     * Test conveyancers can update information
     *
     * @return void
     */
    public function test_conveyancers_can_update_information()
    {
        $new_name = 'New Company Name';
        $new_company_number = '12345678';
        $new_sra_clc_number = '123456';
        $new_trading_name = 'New Trading Name';
        $new_vat_number = '123456';
        $new_website = 'http://example.com';
        $new_location = 'New Location';
        $new_telephone_number = '1234567890';
        $new_email_address = 'new@example.com';

        $args = [
            'name' => $new_name,
            'company_number' => $new_company_number,
            'sra_clc_number' => $new_sra_clc_number,
            'address' => [
                'line_1' => $this->conveyancer->address->line_1,
                'line_2' => $this->conveyancer->address->line_2,
                'city' => $this->conveyancer->address->city,
                'postcode' => $this->conveyancer->address->postcode,
            ],
            'trading_name' => $new_trading_name,
            'vat_number' => $new_vat_number,
            'website' => $new_website,
            'location' => $new_location,
            'telephone_number' => $new_telephone_number,
            'email_address' => $new_email_address,
        ];

        $this->updateConveyancerDetailsMutation($this->user, $args)->assertJsonFragment([
            'name' => $new_name,
            'company_number' => $new_company_number,
            'sra_clc_number' => $new_sra_clc_number,
            'trading_name' => $new_trading_name,
            'vat_number' => $new_vat_number,
            'website' => $new_website,
            'location' => $new_location,
            'telephone_number' => $new_telephone_number,
            'email_address' => $new_email_address,
        ]);
    }

    /**
     * Test conveyancers can update information
     *
     * @return void
     */
    public function test_cannot_submit_blank_fields()
    {
        $new_name = '';
        $new_company_number = '';
        $new_sra_clc_number = '';
        $new_trading_name = '';
        $new_vat_number = '';
        $new_website = '';
        $new_location = '';
        $new_telephone_number = '';
        $new_email_address = '';

        $args = [
            'name' => $new_name,
            'company_number' => $new_company_number,
            'sra_clc_number' => $new_sra_clc_number,
            'address' => [
                'line_1' => $this->conveyancer->address->line_1,
                'line_2' => $this->conveyancer->address->line_2,
                'city' => $this->conveyancer->address->city,
                'postcode' => $this->conveyancer->address->postcode,
            ],
            'trading_name' => $new_trading_name,
            'vat_number' => $new_vat_number,
            'website' => $new_website,
            'location' => $new_location,
            'telephone_number' => $new_telephone_number,
            'email_address' => $new_email_address,
        ];

        $this->updateConveyancerDetailsMutation($this->user, $args)
        ->assertJsonFragment(
            [
                'validation' => [
                    'input.name' => [
                        'A name is required',
                    ],
                    'input.company_number' => [
                        'A company number is required',
                    ],
                ],
            ]);
    }
}
