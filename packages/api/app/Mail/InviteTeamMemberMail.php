<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteTeamMemberMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitedUser;

    public $currentUser;

    public $conveyancer;

    public $inviteCode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $invitedUser, User $currentUser)
    {
        $this->invitedUser = $invitedUser;
        $this->currentUser = $currentUser;
        $this->conveyancer = $currentUser->conveyancer;
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
            ->markdown('mail.conveyancer.invite-team-member')
            ->with([
                'to' => $this->invitedUser,
                'currentUser' => $this->currentUser,
                'conveyancer' => $this->conveyancer,
                'inviteCode' => $this->inviteCode,
            ]);
    }
}
