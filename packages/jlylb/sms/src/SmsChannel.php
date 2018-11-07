<?php

namespace Jlylb\Sms;

use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

/**
 * 短信通道
 *
 * @author jlylb
 */
class SmsChannel {

    protected $smsc;

    public function __construct(SmsClient $smsc) {
        $this->smsc = $smsc;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return void
     */
    public function send($notifiable, Notification $notification) {
        if (!($to = $this->getRecipients($notifiable, $notification))) {
            return;
        }
        $message = $notification->toSms($notifiable);
        if (\is_string($message)) {
            $message = new SmsMessage($message);
        }
        $this->sendMessage($to, $message);
    }

    /**
     * Gets a list of phones from the given notifiable.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return string[]
     */
    protected function getRecipients($notifiable, Notification $notification) {
        if(!is_object($notifiable)) {
            return [$notifiable];
        }
        $to = $notifiable->routeNotificationFor('sms', $notification);
        if ($to === null || $to === false || $to === '') {
            return [];
        }
        return \is_array($to) ? $to : [$to];
    }

    protected function sendMessage($recipients, SmsMessage $message) {
//        if (\mb_strlen($message->content) > 800) {
//            throw CouldNotSendNotification::contentLengthLimitExceeded();
//        }
        $params = [
            'phones' => $message->to ?: \implode(',', $recipients),
            'mes' => $message->content,
            'sender' => $message->from,
        ];
        if ($message->sendAt instanceof \DateTimeInterface) {
            $params['time'] = '0' . $message->sendAt->getTimestamp();
        }
        $this->smsc->send($params);
    }

}
