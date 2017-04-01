<?php

namespace base\services;

use yii\rbac\ManagerInterface;
use base\services\interfaces\AuthManagerInterface;

/**
 * Class AuthManager
 * @package base\services
 */
class AuthManager implements AuthManagerInterface
{
    private $manager;

    /**
     * AuthManager constructor.
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @inheritdoc
     */
    public function assign($role, $userId)
    {
        $this->manager->assign($this->manager->getRole($role), $userId);
    }
}
