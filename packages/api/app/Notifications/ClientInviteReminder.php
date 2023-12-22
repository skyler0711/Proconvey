<?php

namespace App\Notifications;

use App\Mail\ClientInviteReminderMail;
use App\Models\Conveyancer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class ClientInviteReminder extends Notification
{
    use Queueable, SerializesModels;

    public $user;

    public $conveyancer;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Conveyancer $conveyancer)
    {
        $this->user = $user;
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
        return (new ClientInviteReminderMail($this->user, $this->conveyancer))
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
