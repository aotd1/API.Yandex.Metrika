<?php namespace Yandex;

/**
 * Yandex API wrapper.
 * - First of all you need to register new console application @link https://oauth.yandex.ru/client/new
 *   with redirect_url https://oauth.yandex.ru/verification_code
 * - Next - take <access_token>:
 *   https://oauth.yandex.ru/authorize?response_type=code&client_id=<client_id>
 * - Use $this->getTokenByCode() to obtain access_token
 */
class ApiBase
{
    private $client_id;
    private $client_secret;

    protected static $service = 'https://api.yandex.ru';

    public $access_token;
    public static $certificate_path = __DIR__;

    public function __construct($client_id = null, $client_secret = null, $access_token = null)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->access_token = $access_token;
    }

    /**
     * @param string $code
     * @throws ApiException on fail
     * @return array
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
            throw new ApiException("Can't take access_token by application code");
        $data = json_decode($data, true);
        if (isset($data['error']))
            throw new ApiException("Something went wrong: {$data['error']}");
        if (!isset($data['access_token']))
            throw new ApiException("No errors, but token not send: " . print_r($data, true));
        return $data;
    }

    protected function request($method = 'GET', $url, $options = array())
    {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query(array('oauth_token' => $this->access_token));
        return json_decode(self::rawRequest($method, static::$service . $url, $options), true);
    }

    /**
     * Send request with token
     * @param string $method
     * @param string $url
     * @param array $options
     * @throws ApiException
     * @return mixed
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

            // Download cacert.pem from http://curl.haxx.se/docs/caextract.html
            // (Automatically converted CA Certs from mozilla.org) 
            CURLOPT_CAINFO => self::$certificate_path . '/cacert.pem',
            
        );

        switch (strtoupper($method)) {
            case 'DELETE': $curlOpt[CURLOPT_CUSTOMREQUEST] = "DELETE";
            case 'GET':
                if (!empty($options))
                    $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($options);
                break;
            case 'PUT':
                $body = http_build_query($options);
                $fp = fopen('php://temp/maxmemory:256000', 'w');
                if (!$fp)
                    throw new ApiException('Could not open temp memory data');
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
                throw new ApiException("Unsupported request method '$method'");
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
            throw new ApiException($err_msg, $err_no);
        }
    }

    /**
     * @param $value
     * @param $dictionaryName
     * @return mixed
     * @throws ApiException
     */
    protected static function checkDictionary($value, $dictionaryName)
    {
        $dictionaryName = ucfirst($dictionaryName);
        if (empty(static::${'dict'.$dictionaryName}))
            throw new ApiException("Unsupported dictionary: '$dictionaryName'. You must specify `public static \$dict$dictionaryName` array");
        if (!empty($value) && !in_array($value, static::${'dict'.$dictionaryName}))
            throw new ApiException("Unsupported value: '$value'");
        return $value;
    }

    /**
     * @param datetime $date
     * @return string
     */
    protected static function formatDate($date)
    {
        if (is_string($date)) {
            $date = strtotime($date);
        }
        return date('Ymd', $date);
    }

}

