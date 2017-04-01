<?php

namespace base\services\interfaces;

/**
 * Interface AuthManagerInterface
 * @package base\services\interfaces
 */
interface AuthManagerInterface
{
    /**
     * @param $role
     * @param $userId
     * @return mixed
     */
    public function assign($role, $userId);
}
