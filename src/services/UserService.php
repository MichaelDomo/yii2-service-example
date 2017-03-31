<?php

namespace michaeldomo\service\services;

use michaeldomo\service\repositories\UserRepository;
use michaeldomo\service\models\User;

class UserService
{
    private $userRepository;
    private $passwordHasher;
    private $authTokenizer;

    public function __construct(
        UserRepository $userRepository,
        AuthTokenizer $authTokenizer,
        PasswordHasher $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->authTokenizer = $authTokenizer;
        $this->passwordHasher = $passwordHasher;
    }

    public function requestSignup($username, $email, $password)
    {
        $this->guardUsernameIsUnique($username);
        $this->guardEmailIsUnique($email);
        $user = User::requestSignup(
            $username,
            $email,
            $password,
            $this->passwordHasher,
            $this->authTokenizer
        );

        /**
         * Отправляем письмо
         * Логируем добавление нового пользователя
         * etc
         */

        $this->userRepository->add($user);
    }

    public function confirmSignup($token)
    {
        $user = $this->userRepository->findByEmailConfirmToken($token);
        $this->guardIsValidEmailConfirmToken($token);
        $user->confirmSignup();
        $this->userRepository->save($user);
    }

    private function guardIsValidEmailConfirmToken($token)
    {
        if (!$this->authTokenizer->validate($token)) {
            throw new \DomainException('Token is not valid');
        }
    }

    private function guardUsernameIsUnique($username)
    {
        if ($this->userRepository->existsByUsername($username)) {
            throw new \DomainException('Username already exists');
        }
    }

    private function guardEmailIsUnique($email)
    {
        if ($this->userRepository->existsByEmail($email)) {
            throw new \DomainException('Email already exists');
        }
    }
}
