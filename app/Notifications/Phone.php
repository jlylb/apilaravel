<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Jlylb\Sms\SmsMessage;
use Jlylb\Sms\SmsChannel;

class Phone extends Notification  implements ShouldQueue
{
    use Queueable;
    
    protected  $info;

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
        return [SmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toSms($notifiable)
    {
        return (new SmsMessage)
                ->to($this->info['phone'])
                ->content($this->info['content']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->info;
    }
}
