<?php

namespace michaeldomo\service\services;

use michaeldomo\service\repositories\UserRepository;
use michaeldomo\service\models\User;
use michaeldomo\service\services\interfaces\LoggerInterface;
use michaeldomo\service\services\interfaces\NotifierInterface;

class UserService
{
    private $userRepository;
    private $passwordHasher;
    private $authTokenizer;
    private $logger;
    private $notifier;

    public function __construct(
        UserRepository $userRepository,
        AuthTokenizer $authTokenizer,
        PasswordHasher $passwordHasher,
        LoggerInterface $logger,
        NotifierInterface $notifier
    ) {
        $this->userRepository = $userRepository;
        $this->authTokenizer = $authTokenizer;
        $this->passwordHasher = $passwordHasher;
        $this->logger = $logger;
        $this->notifier = $notifier;
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
        $this->userRepository->add($user);
        $this->notifier->notify('user/signup', ['model' => $user], $user->email, 'You are joined!');
        $this->logger->log('User ' . $user->id . ' is created');
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
