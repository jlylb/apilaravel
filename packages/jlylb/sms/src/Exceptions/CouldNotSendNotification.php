<?php
namespace Jlylb\Sms\Exceptions;

use Exception;

class CouldNotSendNotification extends Exception
{
    /**
     * @param Exception $exception
     * @return static
     */
    public static function serviceRespondedWithAnError(Exception $exception)
    {
        return new static("sms service responded with an error '{$exception->getCode()}: {$exception->getMessage()}'");
    }
    
     /**
     * Thrown when we're unable to communicate with smsc.ru.
     *
     * @param  Exception  $exception
     *
     * @return static
     */
    public static function couldNotCommunicateWithSmsc(Exception $exception)
    {
        return new static(
            "The communication with sms failed. Reason: {$exception->getMessage()}".$exception->getCode()
        );
    }
}
