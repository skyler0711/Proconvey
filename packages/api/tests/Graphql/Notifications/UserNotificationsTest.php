<?php

namespace Tests\Graphql\Notifications;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\ExampleNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserNotificationsTest extends TestCase
{
    private $user;

    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->user = User::factory()->create([
            'role' => UserRole::Conveyancer,
        ]);

        $notification = new ExampleNotification();

        $this->data = $notification->toArray($this->user);

        DB::insert('insert into notifications (id, type, notifiable_type, notifiable_id, data, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)', [
            1,
            'App\Notifications\ExampleNotification',
            'user',
            $this->user->id,
            json_encode($this->data),
            Carbon::now(),
            Carbon::now(),
        ]);
    }

    public function test_user_can_see_notifications()
    {
        $this->assertTrue($this->user->unreadNotifications()->count() > 0);

        $repsonse = $response = $this
            ->actingAs($this->user)
            ->graphQL(
                /** @lang GraphQL */
                '
            query me {
                me {
                    id
                    unread_notifications {
                        id
                        type
                        notifiable_type
                        notifiable_id
                        data {
                            type
                            id
                            message
                        }
                        read_at 
                        created_at
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
                        'id' => $this->user->id,
                        'unread_notifications' => [
                            [
                                'id' => 1,
                                'type' => 'App\Notifications\ExampleNotification',
                                'notifiable_id' => $this->user->id,
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function test_user_cannot_see_other_user_notifications()
    {
        $altUser = User::factory()->create([
            'role' => UserRole::Conveyancer,
        ]);

        DB::insert('insert into notifications (id, type, notifiable_type, notifiable_id, data, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)', [
            2,
            'App\Notifications\ExampleNotification',
            'user',
            $altUser->id,
            json_encode($this->data),
            Carbon::now(),
            Carbon::now(),
        ]);

        $this->assertEquals($this->user->unreadNotifications()->count(), 1);

        $results = DB::select('select * from notifications');

        $this->assertEquals(count($results), 2);

        $repsonse = $response = $this
            ->actingAs($this->user)
            ->graphQL(
                /** @lang GraphQL */
                '
        query me {
            me {
                id
                unread_notifications {
                    id
                    type
                    notifiable_type
                    notifiable_id
                    data {
                        type
                        id
                        message
                    }
                    read_at 
                    created_at
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
                        'id' => $this->user->id,
                        'unread_notifications' => [
                            [
                                'id' => 1,
                                'type' => 'App\Notifications\ExampleNotification',
                                'notifiable_id' => $this->user->id,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertJsonCount(1, 'data.me.unread_notifications');
    }

    public function test_unauthenticated_user_cannot_see_notifications()
    {
        $repsonse = $response = $this
            ->graphQL(
                /** @lang GraphQL */
                '
            query me {
                me {
                    id
                    unread_notifications {
                        id
                        type
                        notifiable_type
                        notifiable_id
                        data {
                            type
                            id
                            message
                        }
                        read_at 
                        created_at
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
                    'me' => null,
                ],
            ]);
    }

    public function test_user_can_mark_notifications_as_read()
    {
        $this->assertTrue($this->user->unreadNotifications()->count() > 0);

        $response = $this
            ->actingAs($this->user)
            ->graphQL(
                /** @lang GraphQL */
                '
            query me {
                me {
                    id
                    unread_notifications {
                        id
                        type
                        notifiable_type
                        notifiable_id
                        data {
                            type
                            id
                            message
                        }
                        read_at 
                        created_at
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
                        'id' => $this->user->id,
                        'unread_notifications' => [
                            [
                                'id' => 1,
                                'type' => 'App\Notifications\ExampleNotification',
                                'notifiable_id' => $this->user->id,
                            ],
                        ],
                    ],
                ],
            ]);

        $response = $this
            ->actingAs($this->user)
            ->graphQL(
                /** @lang GraphQL */
                '
                mutation markAllNotificationsRead {
                    markAllNotificationsRead {
                      id
                      unread_notifications {
                            id
                            type
                            notifiable_type
                            notifiable_id
                            data {
                                type
                                id
                                message
                            }
                            read_at
                            created_at
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
                    'markAllNotificationsRead' => [
                        'id' => $this->user->id,
                        'unread_notifications' => [],
                    ],
                ],
            ]);

        $this->assertTrue($this->user->unreadNotifications()->count() === 0);
    }
}
