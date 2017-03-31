<?php

namespace michaeldomo\service\forms;

use michaeldomo\service\repositories\UserRepository;

class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    private $userRepository;

    public function __construct(UserRepository $userRepository, $config = [])
    {
        $this->userRepository = $userRepository;
        parent::__construct($config);
    }

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

    public function validateUsername($attribute)
    {
        if ($this->userRepository->existsByUsername($this->$attribute)) {
            $this->addError($attribute, 'This username has already been taken.');
        }
    }

    public function validateEmail($attribute)
    {
        if ($this->userRepository->existsByEmail($this->$attribute)) {
            $this->addError($attribute, 'This email has already been taken.');
        }
    }
}
