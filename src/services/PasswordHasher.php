<?php

namespace michaeldomo\service\services;

use yii\base\Security;

class PasswordHasher
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function generate($password)
    {
        return $this->security->generatePasswordHash($password);
    }

    public function validate($password, $passwordHash)
    {
        return $this->security->validatePassword($password, $passwordHash);
    }
}
