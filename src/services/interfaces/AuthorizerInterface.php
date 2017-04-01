<?php

namespace base\services\interfaces;

/**
 * Interface AuthorizerInterface
 * @package base\services\interfaces
 */
interface AuthorizerInterface
{
    /**
     * @param $user
     * @param $rememberMe
     * @return mixed
     */
    public function login($user, $rememberMe);
    /**
     * @return mixed
     */
    public function logout();
}
