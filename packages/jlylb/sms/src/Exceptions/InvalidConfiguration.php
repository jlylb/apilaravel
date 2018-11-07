<?php
namespace Jlylb\Sms\Exceptions;

use Exception;
/**
 * 配置异常
 *
 * @author jlylb
 */
class InvalidConfiguration extends Exception
{
    /**
     * @return static
     */
    public static function configurationNotSet()
    {
        return new static('In order to send notification via sms you need to add credentials in the `sms` key of `config.services`.');
    }
}
