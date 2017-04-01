<?php

namespace base\services;

use base\services\interfaces\AuthorizerInterface;
use yii\web\User;

/**
 * Class Authorizer
 * @package base\services
 */
class Authorizer implements AuthorizerInterface
{
    private $authorizer;
    private $duration;

    /**
     * Authorizer constructor.
     * @param User $authorizer
     * @param $duration
     */
    public function __construct(User $authorizer, $duration)
    {
        $this->authorizer = $authorizer;
        $this->duration = $duration;
    }

    /**
     * @inheritdoc
     */
    public function login($user, $rememberMe)
    {
        $this->authorizer->login($user, $rememberMe ? $this->duration : 0);
    }

    /**
     * @inheritdoc
     */
    public function logout()
    {
        $this->authorizer->logout();
    }
}
