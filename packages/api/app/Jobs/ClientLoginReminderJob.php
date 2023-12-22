<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ClientInviteReminder;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClientLoginReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = User::query()
            ->where('created_at', '<', Carbon::now()->subDays(1))
            ->whereNull('email_verified_at')
            ->get();

        $users->map(function ($user) {
            $conveyancer = $user->properties->first()->conveyancer;
            $user->notify(new ClientInviteReminder($user, $conveyancer));
        });
    }
}
