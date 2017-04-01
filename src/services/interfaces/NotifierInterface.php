<?php

namespace base\services\interfaces;

/**
 * Interface NotifierInterface
 * @package base\services\interfaces
 */
interface NotifierInterface
{
    /**
     * @param $view
     * @param $params
     * @param $email
     * @param $subject
     * @return mixed
     */
    public function notify($view, $email, $subject, $params);
}
