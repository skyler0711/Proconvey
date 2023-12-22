<?php

namespace App\Mail;

use App\Models\Address;
use App\Models\Conveyancer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitedUser;

    public $currentUser;

    public $address;

    public $conveyancer;

    public $inviteCode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $invitedUser, User $currentUser, Address $address, Conveyancer $conveyancer)
    {
        $this->invitedUser = $invitedUser;
        $this->currentUser = $currentUser;
        $this->address = $address;
        $this->conveyancer = $conveyancer;
        $this->inviteCode = bcrypt($invitedUser->invite_code);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('You have been invited to ProConvey!')
            ->markdown('mail.client.invite-client')
            ->with([
                'to' => $this->invitedUser,
                'currentUser' => $this->currentUser,
                'address' => $this->address,
                'conveyancer' => $this->conveyancer,
                'inviteCode' => $this->inviteCode,
            ]);
    }
}
