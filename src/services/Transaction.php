<?php

namespace base\services;

/**
 * Class Transaction
 * @package base\services
 */
class Transaction
{
    private $transaction;

    /**
     * Transaction constructor.
     * @param \yii\db\Transaction $transaction
     */
    public function __construct(\yii\db\Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Commit transaction.
     */
    public function commit()
    {
        $this->transaction->commit();
    }

    /**
     * Rollback transaction.
     */
    public function rollback()
    {
        $this->transaction->rollBack();
    }
}
