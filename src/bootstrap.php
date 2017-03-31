<?php

namespace michaeldomo\service;

use Yii;
use michaeldomo\service\services\interfaces\LoggerInterface;
use michaeldomo\service\services\interfaces\NotifierInterface;
use michaeldomo\service\services\Logger;
use yii\base\BootstrapInterface;
use michaeldomo\service\services\AuthTokenizer;
use michaeldomo\service\services\Notifier;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = Yii::$container;
        $container->setSingleton(AuthTokenizer::class, function () {
            return new AuthTokenizer(Yii::$app->security, 96000);
        });
        $container->setSingleton(NotifierInterface::class, function () use ($app) {
            return new Notifier('admin@service.com');
        });
        $container->setSingleton(LoggerInterface::class, Logger::class);
    }
}
