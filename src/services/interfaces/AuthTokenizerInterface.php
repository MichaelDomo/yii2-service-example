<?php

namespace base\services\interfaces;

/**
 * Interface AuthTokenizerInterface
 * @package base\services\interfaces
 */
interface AuthTokenizerInterface
{
    /**
     * Token generation.
     *
     * @return string
     */
    public function generate();
    /**
     * Token validation algorithm, if need.
     *
     * @param $token
     * @return boolean
     */
    public function validate($token);
}
