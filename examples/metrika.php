<?php
require dirname(__FILE__) . '/../vendor/autoload.php';

$config = require dirname(__FILE__) . '/config.php';
\Yandex\ApiBase::$certificate_path = __DIR__;

$metrika = new \Yandex\Metrika($config['client_id'], $config['client_secret'], $config['access_token']);

//Print all counters
printTable($metrika->getCounters());

//Search counter by name
printTable(array($metrika->getCounterByName('aotd.ru')));

function printTable($data) {
    $table = new \Elkuku\Console\Helper\ConsoleTable();
    $table->setHeaders(array_keys($data[0]));
    $table->addData($data);
    echo $table->getTable()."\n";
    unset($table);
}