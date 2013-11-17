<?php
require dirname(__FILE__) . '/../vendor/autoload.php';

$config = require dirname(__FILE__) . '/config.php';
\Yandex\ApiBase::$certificate_path = __DIR__;

$metrika = new \Yandex\Metrika($config['client_id'], $config['client_secret'], $config['access_token']);

//Print all counters
printTable($metrika->getCounters(), "Full counters list");

//Search counter by name
printTable(array($metrika->getCounterByName('Демо доступ к API Метрики')), "Find counter by name");

function printTable($data, $heading = '') {
    echo "$heading:\n";
    if (empty($data) || empty($data[0])) {
        echo "+ No data +\n";
        return;
    }
    $table = new \Elkuku\Console\Helper\ConsoleTable();
    $table->setHeaders(array_keys($data[0]));
    $table->addData($data);
    echo $table->getTable()."\n";
    unset($table);
}