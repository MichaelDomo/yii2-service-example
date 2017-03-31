<?php

namespace michaeldomo\service;

use Yii;
use yii\base\BootstrapInterface;
use michaeldomo\service\services\AuthTokenizer;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = Yii::$container;
        $container->setSingleton(AuthTokenizer::class, function () {
            return new AuthTokenizer(
                Yii::$app->security,
                96000
            );
        });
    }
}
