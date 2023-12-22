<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testPasswordIsHashed(): void
    {
        $randomWord = fake()->word();

        $user = User::factory()->make([
            'password' => $randomWord,
        ]);

        $user->save();

        $this->assertNotEquals($randomWord, $user->password);
    }

    public function testInviteCodeIsHashed(): void
    {
        $randomWord = fake()->word();

        $user = User::factory()->make([
            'invite_code' => Hash::make($randomWord),
        ]);

        $user->save();

        $this->assertNotEquals($randomWord, $user->invite_code);
    }
}
