<?php

namespace App\Mail;

use App\Models\Conveyancer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientInviteReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $conveyancer;

    public $inviteCode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Conveyancer $conveyancer)
    {
        $this->user = $user;
        $this->conveyancer = $conveyancer;
        $this->inviteCode = bcrypt($user->invite_code);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Complete your invite to ProConvey!')
            ->markdown('mail.client.invite-client-reminder')
            ->with([
                'to' => $this->user,
                'address' => $this->user->address,
                'conveyancer' => $this->conveyancer,
                'type' => $this->user->properties->first()->type,
                'inviteCode' => $this->inviteCode,
            ]);
    }
}
