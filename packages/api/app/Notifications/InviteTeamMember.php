<?php

namespace App\Notifications;

use App\Mail\InviteTeamMemberMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class InviteTeamMember extends Notification
{
    use Queueable, SerializesModels;

    public $invitedUser;

    public $currentUser;

    public $invitedUserCode;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $invitedUser, User $currentUser)
    {
        $this->invitedUser = $invitedUser;
        $this->currentUser = $currentUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new InviteTeamMemberMail($this->invitedUser, $this->currentUser))
            ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
