<?php

namespace Tests\Feature;

use App\Enums\UserJobRole;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateUserDetailsTest extends TestCase
{
    private $user;

    private $fake_phone;

    private $old_password;

    private $new_password;

    protected function setUp(): void
    {
        parent::setUp();

        $this->old_password = '0r!ginalP455W0rd';
        $this->new_password = '4V3ryC0mpl!c4t3dP455W0rd';

        $this->user = User::factory()->create([
            'role' => UserRole::Client,
            'password' => $this->old_password,
        ]);

        $this->fake_phone = fake()->mobileNumber();
    }

    private function updateUserMutation($user, $args)
    {
        $instance = $this;

        if ($user) {
            $instance = $this->actingAs($user);
        }

        return $instance
            ->graphQL(
                /** @lang GraphQL */
                '
                mutation updateUserProfile ($input: UpdateUserProfileInput!) {
                    updateUserProfile(input: $input) {
                        first_name
                        last_name
                        email
                        title
                        suffix
                        phone
                    }
                  } 
            ',
                [
                    'input' => $args,
                ],
            );
    }

    public function test_user_can_update_profile()
    {
        $args = [
            'first_name' => 'Testing1',
            'last_name' => 'Testing2',
            'email' => 'new@email.com',
            'title' => 'Mr',
            'suffix' => 'BSc',
            'phone' => $this->fake_phone,
            'job_bio' => 'I am job bio',
            'job_role' => UserJobRole::getRandomValue(),
            'password' => $this->old_password,
            'newPassword' => $this->new_password,
        ];

        $this->updateUserMutation($this->user, $args)->assertJson([
            'data' => [
                'updateUserProfile' => [
                    'first_name' => 'Testing1',
                    'last_name' => 'Testing2',
                    'email' => 'new@email.com',
                    'title' => 'Mr',
                    'suffix' => 'BSc',
                    'phone' => $this->fake_phone,
                ],
            ],

        ]);
    }

    public function test_user_can_change_password_and_login()
    {
        $old_password_hash = $this->user->password;

        $args = [
            'first_name' => 'Testing1',
            'last_name' => 'Testing2',
            'email' => $this->user->email,
            'title' => 'Mr',
            'suffix' => 'BSc',
            'phone' => $this->fake_phone,
            'job_bio' => 'I am job bio',
            'job_role' => UserJobRole::getRandomValue(),
            'password' => $this->old_password,
            'newPassword' => $this->new_password,
        ];

        $this->updateUserMutation($this->user, $args);

        $this->assertDatabaseMissing('users', [
            'password' => $old_password_hash,
        ]);

        $this
        ->actingAs($this->user)
        ->graphQL(/** @lang GraphQL */ '
            mutation Login($input: LoginInput!) {
                login(input: $input) {
                    first_name
                    last_name
                    email
                }
            }
        ', [
            'input' => [
                'email' => $this->user->email,
                'password' => $this->new_password,
            ],
        ])
        ->assertGraphQLErrorFree()
        ->assertGraphQLValidationPasses()
        ->assertJsonFragment([
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
        ]);
    }

    public function test_password_validation_rules()
    {
        $old_password_hash = $this->user->password;

        $weak_password = 'pass';

        $args = [
            'first_name' => 'Testing1',
            'last_name' => 'Testing2',
            'email' => $this->user->email,
            'title' => 'Mr',
            'suffix' => 'BSc',
            'phone' => $this->fake_phone,
            'job_bio' => 'I am job bio',
            'job_role' => 'Assistant',
            'password' => $this->old_password,
            'newPassword' => $weak_password,
        ];

        $this->updateUserMutation($this->user, $args)->assertSee(
            'The password must be at least 8 characters, and be a mix of upper and lower case characters',
        );

        $args = [
            'first_name' => 'Testing1',
            'last_name' => 'Testing2',
            'email' => $this->user->email,
            'title' => 'Mr',
            'suffix' => 'BSc',
            'phone' => $this->fake_phone,
            'job_bio' => 'I am job bio',
            'job_role' => 'Assistant',
            'password' => $this->old_password,
            'newPassword' => $this->old_password,
        ];

        $this->updateUserMutation($this->user, $args)->assertSee(
            'The new password must be different from the current password.',
        );

        $this
        ->actingAs($this->user)
        ->graphQL(/** @lang GraphQL */ '
            mutation Login($input: LoginInput!) {
                login(input: $input) {
                    first_name
                    last_name
                    email
                }
            }
        ', [
            'input' => [
                'email' => $this->user->email,
                'password' => $this->new_password,
            ],
        ])
        ->assertSee('These credentials do not match our records.');
    }

    public function test_user_can_upload_profile_image()
    {
        Storage::fake();

        [$tempPath, $filePath] = $this->generateMockImage();

        Storage::assertExists($tempPath);

        $args = [
            'first_name' => 'Testing1',
            'last_name' => 'Testing2',
            'email' => $this->user->email,
            'title' => 'Mr',
            'suffix' => 'BSc',
            'phone' => $this->fake_phone,
            'job_bio' => 'I am job bio',
            'job_role' => UserJobRole::getRandomValue(),
            'password' => $this->old_password,
            'newPassword' => $this->new_password,
            'profile_image' => [
                'key' => $tempPath,
                'extension' => 'png',
            ],
        ];

        $this->updateUserMutation($this->user, $args);

        $profileImage = User::find($this->user->id)->getFirstMedia('profile_image');

        $this->assertEquals($profileImage->file_name, $filePath);

        Storage::assertMissing($tempPath);
    }
}
