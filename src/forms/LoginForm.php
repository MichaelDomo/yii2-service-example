<?php

namespace frontend\modules\user\forms;

use Yii;
use yii\base\Model;
use base\services\interfaces\PasswordHasherInterface;
use base\repositories\UserRepository;

/**
 * Class LoginForm
 * @package frontend\modules\user\models
 *
 * @property null|\common\models\User $user
 */
class LoginForm extends Model
{
    public $identity;
    public $password;
    public $rememberMe = true;

    private $userRepository;
    private $passwordHasher;

    /**
     * LoginForm constructor.
     * @param UserRepository $userRepository
     * @param PasswordHasherInterface $passwordHasher
     * @param array $config
     */
    public function __construct(
        UserRepository $userRepository,
        PasswordHasherInterface $passwordHasher,
        array $config = []
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identity', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if ($this->userRepository->existsByUsernameOrEmail($this->identity)) {
            $user = $this->userRepository->findByUsernameOrEmail($this->identity);
            if ($this->passwordHasher->validate($this->password, $user->password_hash)) {
                return;
            }
        }
        $this->addError('password', Yii::t('frontend', 'Incorrect username or password.'));
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'identity' => Yii::t('frontend', 'Username or email'),
            'password' => Yii::t('frontend', 'Password'),
            'rememberMe' => Yii::t('frontend', 'Remember Me'),
        ];
    }
}
