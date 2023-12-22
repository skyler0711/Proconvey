<?php

namespace App\Notifications;

use App\Mail\InviteClientMail;
use App\Models\Address;
use App\Models\Conveyancer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class InviteClient extends Notification
{
    use Queueable, SerializesModels;

    public $invitedUser;

    public $currentUser;

    public $conveyancer;

    public $address;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $invitedUser, User $currentUser, Address $address, Conveyancer $conveyancer)
    {
        $this->invitedUser = $invitedUser;
        $this->currentUser = $currentUser;
        $this->address = $address;
        $this->conveyancer = $conveyancer;
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
        return (new InviteClientMail($this->invitedUser, $this->currentUser, $this->address, $this->conveyancer))
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
