<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackgroundProcessErrorNotification extends Notification
{
    use Queueable;

    protected $message,$e;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(String $message, \Exception $e=null)
    {
        $this->message = $message;
        $this->e = $e;
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
        return (new MailMessage)
                    ->line('An error has occured in the application')
                    ->line($this->message)
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            'message'=>$this->message,
            'exception'=>[
                'message'=>$this->e->getMessage() ?? '',
                'file'=>$this->e->getFile() ?? '',
                'line'=>$this->e->getLine() ?? '',
                'code'=>$this->e->getCode() ?? ''
            ]
        ];
    }
}
