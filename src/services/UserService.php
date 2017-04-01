<?php

namespace base\services;

use base\services\interfaces\AuthManagerInterface;
use base\services\interfaces\AuthorizerInterface;
use base\services\interfaces\AuthTokenizerInterface;
use base\services\interfaces\LoggerInterface;
use base\services\interfaces\NotifierInterface;
use base\services\interfaces\PasswordHasherInterface;
use base\repositories\UserProfileRepository;
use base\repositories\UserRepository;
use base\repositories\UserSocialAccountRepository;
use base\repositories\UserTokenRepository;
use common\models\User;
use common\models\UserProfile;
use common\models\UserSocialAccount;
use common\models\UserToken;
use frontend\modules\user\models\clients\ClientInterface;
use yii\helpers\Url;
use cheatsheet\Time;

/**
 * Class UserService
 * @package base\services
 */
class UserService
{
    private $userRepository;
    private $userTokenRepository;
    private $userProfileRepository;
    private $userSocialAccountRepository;
    private $passwordHasher;
    private $authTokenizer;
    private $logger;
    private $notifier;
    private $authManager;
    private $authorizer;
    private $transactionManager;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param UserTokenRepository $userTokenRepository
     * @param UserProfileRepository $userProfileRepository
     * @param UserSocialAccountRepository $userSocialAccountRepository
     * @param AuthTokenizerInterface $authTokenizer
     * @param PasswordHasherInterface $passwordHasher
     * @param LoggerInterface $logger
     * @param NotifierInterface $notifier
     * @param AuthManagerInterface $authManager
     * @param AuthorizerInterface $authorizer
     * @param TransactionManager $transactionManager
     */
    public function __construct(
        UserRepository $userRepository,
        UserTokenRepository $userTokenRepository,
        UserProfileRepository $userProfileRepository,
        UserSocialAccountRepository $userSocialAccountRepository,
        AuthTokenizerInterface $authTokenizer,
        PasswordHasherInterface $passwordHasher,
        LoggerInterface $logger,
        NotifierInterface $notifier,
        AuthManagerInterface $authManager,
        AuthorizerInterface $authorizer,
        TransactionManager $transactionManager
    ) {
        $this->userRepository = $userRepository;
        $this->userTokenRepository = $userTokenRepository;
        $this->userProfileRepository = $userProfileRepository;
        $this->userSocialAccountRepository = $userSocialAccountRepository;
        $this->authTokenizer = $authTokenizer;
        $this->passwordHasher = $passwordHasher;
        $this->logger = $logger;
        $this->notifier = $notifier;
        $this->authManager = $authManager;
        $this->authorizer = $authorizer;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param $username
     * @param $email
     * @param $password
     * @throws \Exception
     */
    public function requestSignup($username, $email, $password)
    {
        $this->guardUsernameIsUnique($username);
        $this->guardEmailIsUnique($email);

        $transaction = $this->transactionManager->begin();
        try {
            $user = User::requestSignup(
                $username,
                $email,
                $this->passwordHasher->hash($password),
                $this->authTokenizer->generate()
            );
            $this->userRepository->add($user);

            $userToken = UserToken::create($user->getId(), $this->authTokenizer, null, Time::SECONDS_IN_A_DAY);
            $this->userTokenRepository->add($userToken);

            $userProfile = UserProfile::create($user->getId());
            $this->userProfileRepository->add($userProfile);

            $this->authManager->assign(User::ROLE_USER, $user->getId());

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        $this->notifier->notify(
            'activation',
            $user->email,
            'Activation email',
            [
                'url' => Url::to(['/user/sign-in/confirm-signup', 'token' => $userToken->token], true)
            ]
        );

        $this->logger->log(
            'user',
            'signup',
            [
                'user_id' => $user->getId(),
                'public_identity' => $user->getPublicIdentity(),
                'created_at' => $user->created_at
            ]
        );
    }

    /**
     * @param $token
     * @throws \Exception
     * TODO Надо добавить логику если действие токена закончилось. Перегенерить его и отправить новое письмо.
     */
    public function confirmSignup($token)
    {
        $this->guardIsValidConfirmToken($token);
        $user = $this->userRepository->findByEmailAndTokenType($token);
        $userToken = $this->userTokenRepository->findActiveTokenByTokenAndType($token);

        $transaction = $this->transactionManager->begin();
        try {
            $user->confirmSignup();
            $this->userRepository->save($user);
            $this->userTokenRepository->delete($userToken);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        $this->authorizer->login($user, false);
    }

    /**
     * @param $identity
     * @param $password
     * @param $rememberMe
     */
    public function requestLogin($identity, $password, $rememberMe)
    {
        $user = $this->userRepository->findByUsernameOrEmail($identity);
        $this->guardIsValidPassword($password, $user->password_hash);
        $this->authorizer->login($user, $rememberMe);
    }

    /**
     * request Logout
     */
    public function requestLogout()
    {
        $this->authorizer->logout();
    }

    /**
     * @param $email
     * TODO Надо добавить логику если действие токена закончилось. Перегенерить его и отправить новое письмо.
     * TODO Добавить логику, если токен есть, а пользователь запросил сброс пароля.
     */
    public function requestPasswordReset($email)
    {
        $this->guardUserIsExistsByEmail($email);
        $user = $this->userRepository->findByUsernameOrEmail($email);
        $userToken = UserToken::create(
            $user->getId(),
            $this->authTokenizer,
            UserToken::TYPE_PASSWORD_RESET,
            Time::SECONDS_IN_A_DAY
        );
        $this->userTokenRepository->add($userToken);
        $this->notifier->notify(
            'passwordReset',
            $email,
            'Password reset',
            [
                'user' => $user,
                'token' => $userToken->token
            ]
        );
    }

    /**
     * @param $token
     * @param $password
     * @throws \Exception
     */
    public function confirmPasswordReset($token, $password)
    {
        $this->guardIsValidConfirmToken($token);
        $user = $this->userRepository->findByEmailAndTokenType($token, UserToken::TYPE_PASSWORD_RESET);
        $userToken = $this->userTokenRepository->findActiveTokenByTokenAndType($token, UserToken::TYPE_PASSWORD_RESET);

        $transaction = $this->transactionManager->begin();
        try {
            $user->changePassword($this->passwordHasher->hash($password));
            $this->userRepository->save($user);
            $this->userTokenRepository->delete($userToken);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    /**
     * @param ClientInterface $client
     * @throws \Exception
     * TODO добавить логику, если email'a нет.
     */
    public function oAuthSignup(ClientInterface $client)
    {
        /** @var UserSocialAccount $socialAccount */
        $socialAccount = $this->userSocialAccountRepository->findByClient($client);

        if (null === $socialAccount) {
            $transaction = $this->transactionManager->begin();
            try {
                $socialAccount = UserSocialAccount::create($client);
                $this->userSocialAccountRepository->add($socialAccount);

                $password = $this->passwordHasher->generate();
                $user = User::requestSignup(
                    $socialAccount->username,
                    $socialAccount->email,
                    $this->passwordHasher->hash($password),
                    $this->authTokenizer->generate(),
                    User::STATUS_ACTIVE
                );
                $this->userRepository->add($user);

                $userProfile = UserProfile::create($user->getId());
                $this->userProfileRepository->add($userProfile);

                $this->authManager->assign(User::ROLE_USER, $user->getId());

                $socialAccount->bindUser($user->getId());
                $this->userSocialAccountRepository->save($socialAccount);

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollback();
                throw $e;
            }

            $this->notifier->notify(
                'oauthWelcome',
                $user->email,
                'Your login information',
                [
                    'user' => $user,
                    'password' => $password
                ]
            );

            $this->logger->log(
                'user',
                'signup',
                [
                    'user_id' => $user->getId(),
                    'public_identity' => $user->getPublicIdentity(),
                    'created_at' => $user->created_at
                ]
            );
        } else {
            $user = $socialAccount->user;
        }

        $this->authorizer->login($user, true);
    }

    /**
     * @param $email
     * @throws \DomainException
     */
    private function guardUserIsExistsByEmail($email)
    {
        if (!$this->userRepository->existsByEmail($email)) {
            throw new \DomainException('Email not exists');
        }
    }

    /**
     * @param $token
     * @throws \DomainException
     */
    private function guardIsValidConfirmToken($token)
    {
        if (!$this->authTokenizer->validate($token)) {
            throw new \DomainException('Token is not valid');
        }
    }

    /**
     * @param $username
     * @throws \DomainException
     */
    private function guardUsernameIsUnique($username)
    {
        if ($this->userRepository->existsByUsername($username)) {
            throw new \DomainException('Username already exists');
        }
    }

    /**
     * @param $password
     * @param $passwordHash
     * @throws \DomainException
     */
    private function guardIsValidPassword($password, $passwordHash)
    {
        if (false === $this->passwordHasher->validate($password, $passwordHash)) {
            throw new \DomainException('Wrong username or password');
        }
    }

    /**
     * @param $email
     * @throws \DomainException
     */
    private function guardEmailIsUnique($email)
    {
        if ($this->userRepository->existsByEmail($email)) {
            throw new \DomainException('Email already exists');
        }
    }
}
