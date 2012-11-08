<?php


/**
 * Yandex API wrapper.
 * - First of all you need to register new console application @link https://oauth.yandex.ru/client/new
 *   with redirect_url https://oauth.yandex.ru/verification_code
 * - Next - take <access_code>:
 *   https://oauth.yandex.ru/authorize?response_type=code&client_id=<client_id>
 * - Use $this->getTokenByCode() to obtain access_token
 */
class YandexApiBase
{
    private $client_id;
    private $client_secret;

    public $access_token;

    public function __construct($token, $id = null, $secret = null)
    {
        $this->client_id = $id;
        $this->client_secret = $secret;
        $this->access_token = $token;
    }

    /**
     * @param string $code
     * @throws YandexApiException on fail
     */
    public function getTokenByCode($code)
    {
        $data = self::rawRequest('POST', 'https://oauth.yandex.ru/token', array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ));
        if (!$data)
            throw new YandexApiException("Can't take access_token by application code");
        $data = json_decode($data);
        if (isset($data['error']))
            throw new YandexApiException("Shit happens: {$data['error']}");
        if (!isset($data['access_token']))
            throw new YandexApiException("No errors, but token not send: " . CVarDumper::dumpAsString($data));
        return $data;

    }

    protected function request($method = 'GET', $url, $options = array())
    {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query(array('oauth_token' => $this->access_token));
        return json_decode(self::rawRequest($method, $url, $options), true);
    }

    /**
     * Send request with token
     * @param $url
     * @param array $options
     * @param string $method
     */
    protected static function rawRequest($method = 'GET', $url, $options = array())
    {
        //Default options for all requests
        $curlOpt = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_MAXREDIRS => 3,

            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,

            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAPATH => dirname(__FILE__) . '/cert',
            CURLOPT_CAINFO => dirname(__FILE__) . '/cert/solid-cert.crt',
        );

        switch (strtoupper($method)) {
            case 'DELETE':
                $curlOpt[CURLOPT_CUSTOMREQUEST] = "DELETE";
            case 'GET':
                if (!empty($options))
                    $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($options);
                break;
            case 'PUT':
                $body = http_build_query($options);
                $fp = fopen('php://temp/maxmemory:256000', 'w');
                if (!$fp)
                    throw new YandexApiException('Could not open temp memory data');
                fwrite($fp, $body);
                fseek($fp, 0);
                $curlOpt[CURLOPT_PUT] = 1;
                $curlOpt[CURLOPT_BINARYTRANSFER] = 1;
                $curlOpt[CURLOPT_INFILE] = $fp; // file pointer
                $curlOpt[CURLOPT_INFILESIZE] = strlen($body);
                break;
            case 'POST':
                $curlOpt[CURLOPT_HTTPHEADER] = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8;');
                $curlOpt[CURLOPT_POST] = true;
                $curlOpt[CURLOPT_POSTFIELDS] = http_build_query($options);
                break;
            default:
                throw new YandexApiException("Unsupported request method '$method'");
        }

        $curl = curl_init($url);
        curl_setopt_array($curl, $curlOpt);
        $return = curl_exec($curl);
        $err_no = curl_errno($curl);
        if ($err_no === 0) {
            curl_close($curl);
            return $return;
        } else {
            $err_msg = curl_error($curl);
            curl_close($curl);
            throw new YandexApiException($err_msg, $err_no);
        }
    }

}

class YandexApiException extends Exception
{
}
