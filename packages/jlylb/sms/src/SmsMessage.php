<?php

namespace Jlylb\Sms;

/**
 * 短信格式
 *
 * @author jlylb
 */
class SmsMessage {

    public $from = '';

    /**
     * The message content.
     *
     * @var string
     */
    public $content = '';

    /**
     * Time of sending a message.
     *
     * @var \DateTimeInterface
     */
    public $sendAt;

    /**
     * Create a new message instance.
     *
     * @param  string $content
     *
     * @return static
     */
    public static function create($content = '') {
        return new static($content);
    }

    /**
     * @param  string  $content
     */
    public function __construct($content = '') {
        $this->content = $content;
    }

    /**
     * Set the message content.
     *
     * @param  string  $content
     *
     * @return $this
     */
    public function content($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the phone number or sender name the message should be sent from.
     *
     * @param  string  $from
     *
     * @return $this
     */
    public function from($from) {
        $this->from = $from;
        return $this;
    }
    
    public function to($to) {
        $this->to = $to;
        return $this;
    }

    /**
     * Set the time the message should be sent.
     *
     * @param  \DateTimeInterface|null  $sendAt
     *
     * @return $this
     */
    public function sendAt(\DateTimeInterface $sendAt = null) {
        $this->sendAt = $sendAt;
        return $this;
    }

}
