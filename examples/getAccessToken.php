<?php
require dirname(__FILE__) . '/../src/Yandex/load.php';
$config = require dirname(__FILE__) . '/config.php';

\Yandex\ApiBase::$certificate_path = __DIR__;
$api = new \Yandex\ApiBase($config['client_id'], $config['client_secret']);
$token = $api->getTokenByCode($config['code']);
var_dump($token);
