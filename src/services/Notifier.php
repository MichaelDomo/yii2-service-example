<?php

namespace base\services;

use Yii;
use common\commands\SendEmailCommand;
use base\services\interfaces\NotifierInterface;

/**
 * Class Notifier
 * @package base\services
 */
class Notifier implements NotifierInterface
{
    /**
     * @inheritdoc
     */
    public function notify($view, $email, $subject, $params)
    {
        Yii::$app->commandBus->handle(new SendEmailCommand([
            'subject' => Yii::t('frontend', $subject),
            'view' => $view,
            'to' => $email,
            'params' => $params
        ]));
    }
}
