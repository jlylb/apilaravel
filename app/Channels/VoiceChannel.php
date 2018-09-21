<?php
namespace App\Channels;

use Illuminate\Notifications\Notification;

class VoiceChannel {
    
    protected $vocie;

    public function __construct($vocie)
    {
        $this->vocie = $vocie;
    }
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toVoice($notifiable);
    }
}
