<?php

namespace base\services;

use base\services\interfaces\PasswordHasherInterface;
use yii\base\Security;

/**
 * Class PasswordHasher
 * @package base\services
 */
class PasswordHasher implements PasswordHasherInterface
{
    private $security;
    private $length;

    /**
     * PasswordHasher constructor.
     * @param Security $security
     * @param $length
     */
    public function __construct(Security $security, $length)
    {
        $this->security = $security;
        $this->length = $length;
    }

    /**
     * @inheritdoc
     */
    public function hash($password)
    {
        return $this->security->generatePasswordHash($password);
    }

    /**
     * @inheritdoc
     */
    public function validate($password, $passwordHash)
    {
        return $this->security->validatePassword($password, $passwordHash);
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return $this->security->generateRandomString($this->length);
    }
}
