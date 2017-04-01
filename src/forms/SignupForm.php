<?php

namespace frontend\modules\user\forms;

use Yii;
use yii\base\Model;
use base\repositories\UserRepository;

/**
 * Class SignupForm
 * @package frontend\modules\user\forms
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    private $userRepository;

    /**
     * SignupForm constructor.
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
            [['username', 'email', 'password'], 'filter', 'filter' => 'trim'],
            [['username', 'email', 'password'], 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'validateUsername'],
            ['email', 'email'],
            ['email', 'validateEmail'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @param $attribute
     */
    public function validateUsername($attribute)
    {
        if ($this->userRepository->existsByUsername($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'This username has already been taken.'));
        }
    }

    /**
     * @param $attribute
     */
    public function validateEmail($attribute)
    {
        if ($this->userRepository->existsByEmail($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'This email address has already been taken.'));
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('frontend', 'Username'),
            'email' => Yii::t('frontend', 'E-mail'),
            'password' => Yii::t('frontend', 'Password'),
        ];
    }
}
