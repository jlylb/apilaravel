<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;

class AlarmNotice extends Notification
{
    use Queueable;
    
    
    protected $info;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($info = [])
    {
        $this->info = $info;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
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
                    ->subject('报警通知')
                    ->view('emails.alarm', ['username'=>$notifiable->name])
                    ->line('重要报警通知')
                    ->line('请速去处理!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        //var_dump($notifiable->toArray());
        return $this->info;
    }
}
