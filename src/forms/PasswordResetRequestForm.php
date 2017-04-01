<?php

namespace frontend\modules\user\forms;

use Yii;
use yii\base\Model;
use base\repositories\UserRepository;

/**
 * Class PasswordResetRequestForm
 * @package frontend\modules\user\forms
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    private $userRepository;

    /**
     * PasswordResetForm constructor.
     * @param UserRepository $userRepository
     * @param array $config
     */
    public function __construct(UserRepository $userRepository, $config = [])
    {
        $this->userRepository = $userRepository;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'validateEmail'],
        ];
    }

    /**
     * @param $attribute
     */
    public function validateEmail($attribute)
    {
        if (false === $this->userRepository->existsByEmail($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'There is no user with such email.'));
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('frontend', 'E-mail')
        ];
    }
}
