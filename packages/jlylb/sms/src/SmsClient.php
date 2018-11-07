<?php

namespace Jlylb\Sms;

use Illuminate\Support\Arr;
use GuzzleHttp\Client as HttpClient;
use Jlylb\Sms\Exceptions\CouldNotSendNotification;

/**
 * 短信客户端
 *
 * @author jlylb
 */
class SmsClient {

    const FORMAT_JSON = 3;

    /** @var HttpClient */
    protected $client;

    /** @var string */
    protected $endpoint;

    /** @var string */
    protected $login;

    /** @var string */
    protected $secret;

    /** @var string */
    protected $sender;

    public function __construct(array $config) {
        $this->login = Arr::get($config, 'username');
        $this->secret = Arr::get($config, 'password');
        $this->sender = Arr::get($config, 'sender');
        $this->endpoint = Arr::get($config, 'url');
        $this->client = new HttpClient([
            'timeout' => 5,
            'connect_timeout' => 5,
        ]);
    }

    public function send($params) {
        $base = [
            'charset' => 'utf-8',
            'login' => $this->login,
            'psw' => $this->secret,
            'sender' => $this->sender,
            'fmt' => self::FORMAT_JSON,
        ];
        $params = \array_merge($base, \array_filter($params));
        try {
//            $response = $this->client->request('POST', $this->endpoint, ['form_params' => $params]);
//            $response = \json_decode((string) $response->getBody(), true);
//            if (isset($response['error'])) {
//                throw new \DomainException($response['error'], $response['error_code']);
//            }
//            return $response;
            // return true;
        } catch (\DomainException $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithSmsc($exception);
        }
    }

}
