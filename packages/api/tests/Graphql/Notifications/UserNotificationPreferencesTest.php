<?php

namespace Tests\Graphql\Notifications;

use App\Models\User;
use App\Models\UserNotificationPreference;
use Tests\TestCase;

class UserNotificationPreferencesTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->has(UserNotificationPreference::factory(), 'notificationPreferences')->create();
    }

    public function test_user_can_see_notification_preferences()
    {
        $response = $this
            ->actingAs($this->user)
            ->graphQL(
                /** @lang GraphQL */
                '
            query me {
                me {
                    notification_preferences {
                        getting_started_forms_completed
                        onboarding_completed
                        client_new_document_uploads
                    }
                }
            }
        '
            );

        $response
            ->assertGraphQLErrorFree()
            ->assertGraphQLValidationPasses()
            ->assertJson([
                'data' => [
                    'me' => [
                        'notification_preferences' => [
                            'getting_started_forms_completed' => true,
                            'onboarding_completed' => true,
                            'client_new_document_uploads' => true,
                        ],
                    ],
                ],
            ]);
    }

    public function test_user_can_update_notification_preferences()
    {
        $response = $this
            ->actingAs($this->user)
            ->graphQL(
                /** @lang GraphQL */
                '

                mutation {
                    updateUserNotificationPreferences(input: {
                        getting_started_forms_completed: false,
                        onboarding_completed: false,
                        client_new_document_uploads: false,
                      }) {
                        notification_preferences  {
                            getting_started_forms_completed
                            onboarding_completed
                            client_new_document_uploads
                        }
                    }
            }
        '
            );

        $response
            ->assertOk()
            ->assertGraphQLErrorFree()
            ->assertGraphQLValidationPasses()
            ->assertJson([
                'data' => [
                    'updateUserNotificationPreferences' => [
                        'notification_preferences' => [
                            'getting_started_forms_completed' => false,
                            'onboarding_completed' => false,
                            'client_new_document_uploads' => false,
                        ],
                    ],
                ],
            ]);
    }
}
