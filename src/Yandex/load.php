<?php
if (!function_exists('curl_init')) {
    throw new Exception('Yandex API SDK needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Yandex API SDK needs the JSON PHP extension.');
}

require 'ApiException.php';
require 'ApiBase.php';
require 'Metrika.php';