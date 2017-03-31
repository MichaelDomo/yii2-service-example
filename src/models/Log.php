<?php

namespace michaeldomo\service\models;

use yii\db\ActiveRecord;

/**
 * Class Log
 * @package michaeldomo\service\models
 *
 * @property $message string
 */
class Log extends ActiveRecord
{
    public static function tableName()
    {
        return 'user';
    }
}
