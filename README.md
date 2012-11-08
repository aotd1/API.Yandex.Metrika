API Yandex Metrika  - Yii extension
===================================

Provides simple interface to access [Yandex.Metrika API](http://api.yandex.ru/metrika/)

It's only sketch!

###Installation
 * Unpack to `protected/extensions`
 * Add the following to your config file 'components' section:
~~~php
<?php
// ...
'metrika' => array(
    'class' => 'application.modules.YandexApi.YandexApiYii',
    'client_id' => '5e0<bla-bla-bla-bla-bla-bla>5f6',
    'client_secret' => 'd8a<bla-bla-bla-bla-bla-bla>9f4',
    'access_token' => '30<bla-bla-bla-bla-bla-bla>73',
    'service' => 'metrika',
),
~~~

###Usage
 * First of all you need to register [new console application](https://oauth.yandex.ru/client/new) with *redirect_url* `https://oauth.yandex.ru/verification_code`
 * Next - take <access_code>:
 * Go to https://oauth.yandex.ru/authorize?response_type=code&client_id=<client_id>
 * Use $this->getTokenByCode() to obtain access_token. If your application only grab data, access_token will not expire.

Somewhere in cli command:
~~~php
class TestCommand extends CConsoleCommand
{

    public function actionIndex(){
        var_dump(Yii::app()->metrika->getCounters());
    }

}
~~~

Will add documentation soon.

