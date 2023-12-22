<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class ClientDocumentNotification extends BaseNotification
{
    use Queueable;

    public $conveyancer;

    public $current_user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($conveyancer, $current_user)
    {
        $this->conveyancer = $conveyancer;
        $this->current_user = $current_user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->current_user['id'],
            'message' => $this->current_user['first_name'].' '.$this->current_user['last_name'].' uploaded a document',
            'type' => 'Document type',

        ];
    }
}
