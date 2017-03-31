<?php

namespace michaeldomo\service\services;

use Yii;

class Notifier implements NotifierInterface
{
    private $fromEmail;

    public function __construct($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    public function notify($view, $data, $email, $subject)
    {
        Yii::$app->mailer->compose($view, $data)
            ->setFrom($this->fromEmail)
            ->setTo($email)
            ->setSubject($subject)
            ->send();
    }
}