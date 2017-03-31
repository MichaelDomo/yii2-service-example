<?php

namespace app\services;

use michaeldomo\service\models\Log;

class Logger implements LoggerInterface
{
    public function log($message)
    {
        $log = new Log();
        $log->message = $message;
        $log->save(false);
    }
}
