<?php
/**
 * User: aotd (work@aotd.ru)
 */
class YandexApiYii extends CApplicationComponent
{
    public $client_id;
    public $client_secret;
    public $access_token;

    public $service = 'metrika';
    public $_service;

    public function init()
    {
        parent::init();
        if (Yii::getPathOfAlias('YandexApi') === false)
            Yii::setPathOfAlias('YandexApi', realpath(dirname(__FILE__)));

        Yii::import('YandexApi.YandexApiBase', true);
        $class = 'Yandex' . ucfirst($this->service);
        Yii::import('YandexApi.' . $class, true);
        $this->_service = new $class($this->access_token, $this->client_id, $this->client_secret);
    }

    /**
     * Proxy fetch allowed callable methods to service
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->_service, $method)) {
            Yii::trace("Call $method of $this->service service");
            return call_user_func_array(array($this->_service, $method), $arguments);
        } else {
            return parent::__call($method, $arguments);
        }
    }

}
