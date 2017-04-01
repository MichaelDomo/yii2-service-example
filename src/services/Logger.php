<?php

namespace base\services;

use Yii;
use common\commands\AddToTimelineCommand;
use base\services\interfaces\LoggerInterface;

/**
 * Class Logger
 * @package base\services
 */
class Logger implements LoggerInterface
{
    /**
     * @inheritdoc
     */
    public function log($category, $event, $data)
    {
        Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'category' => $category,
            'event' => $event,
            'data' => $data
        ]));
    }
}
