<?php
namespace michaeldomo\service\services\interfaces;

interface NotifierInterface
{
    public function notify($view, $data, $email, $subject);
}
