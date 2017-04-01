<?php

namespace base\services\interfaces;

/**
 * Interface PasswordHasherInterface
 * @package base\services\interfaces
 */
interface PasswordHasherInterface
{
    /**
     * @param $password
     * @return mixed
     */
    public function hash($password);
    /**
     * @param $password
     * @param $passwordHash
     * @return mixed
     */
    public function validate($password, $passwordHash);
    /**
     * @return mixed
     */
    public function generate();
}
