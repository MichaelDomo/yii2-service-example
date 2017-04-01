<?php

namespace frontend\modules\user\forms;

use Yii;
use yii\base\Model;

/**
 * Class PasswordResetForm
 * @package frontend\modules\user\forms
 */
class PasswordResetForm extends Model
{
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'filter', 'filter' => 'trim'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password' => Yii::t('frontend', 'Password')
        ];
    }
}
