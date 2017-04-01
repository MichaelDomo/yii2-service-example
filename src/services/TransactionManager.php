<?php

namespace base\services;

use Yii;

/**
 * Class TransactionManager
 * @package base\services
 */
class TransactionManager
{
    /**
     * @return Transaction
     */
    public function begin()
    {
        return new Transaction(Yii::$app->db->beginTransaction());
    }
}
