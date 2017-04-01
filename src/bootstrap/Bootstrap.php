<?php

namespace base\bootstrap;

use Yii;
use yii\base\BootstrapInterface;
use base\services\interfaces\AuthorizerInterface;
use base\services\interfaces\AuthManagerInterface;
use base\services\interfaces\LoggerInterface;
use base\services\interfaces\NotifierInterface;
use base\services\interfaces\PasswordHasherInterface;
use base\services\interfaces\AuthTokenizerInterface;
use base\services\PasswordHasher;
use base\services\Logger;
use base\services\AuthTokenizer;
use base\services\Notifier;
use base\services\AuthManager;
use base\services\Authorizer;

/**
 * Class Bootstrap
 * @package base\bootstrap
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;
        $container->setSingleton(PasswordHasherInterface::class, function () use ($app) {
            return new PasswordHasher(Yii::$app->security, 8);
        });
        $container->setSingleton(AuthTokenizerInterface::class, function () use ($app) {
            return new AuthTokenizer(Yii::$app->security, 40);
        });
        $container->setSingleton(AuthManagerInterface::class, function () use ($app) {
            return new AuthManager(Yii::$app->authManager);
        });
        $container->setSingleton(AuthorizerInterface::class, function () use ($app) {
            return new Authorizer(Yii::$app->user, 720000);
        });
        $container->setSingleton(NotifierInterface::class, Notifier::class);
        $container->setSingleton(LoggerInterface::class, Logger::class);
    }
}
