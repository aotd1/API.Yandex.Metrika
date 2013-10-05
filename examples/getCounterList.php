<?php
require dirname(__FILE__) . '/../src/Yandex/load.php';
$config = require dirname(__FILE__) . '/config.php';

\Yandex\ApiBase::$certificate_path = __DIR__;
$metrika = new \Yandex\Metrika($config['client_id'], $config['client_secret'], $config['access_token']);

//var_dump($metrika->getCounters());
var_dump($metrika->statTrafficSummary(7463896));