<?php

namespace michaeldomo\service\services;

use yii\base\Security;

class AuthTokenizer
{
    private $security;
    private $timeout;

    public function __construct(Security $security, $timeout)
    {
        $this->security = $security;
        $this->timeout = $timeout;
    }

    public function generate()
    {
        return $this->security->generateRandomString() . '_' . time();
    }

    public function validate($token)
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        return $timestamp + $this->timeout >= time();
    }
}
