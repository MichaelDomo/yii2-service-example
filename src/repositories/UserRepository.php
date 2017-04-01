<?php

namespace base\repositories;

use common\models\User;
use common\models\UserToken;
use yii\db\ActiveRecord;

/**
 * Class UserRepository
 * @package base\repositories
 */
class UserRepository
{
    /**
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws \InvalidArgumentException
     */
    public function find($id)
    {
        if (null === ($user = User::findOne($id))) {
            throw new \InvalidArgumentException('Model not found');
        }

        return $user;
    }

    /**
     * @param User $user
     * @throws \InvalidArgumentException
     */
    public function add(User $user)
    {
        if (!$user->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $user->insert(false);
    }

    /**
     * @param User $user
     * @throws \InvalidArgumentException
     */
    public function save(User $user)
    {
        if ($user->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $user->update(false);
    }

    /**
     * В User можно переопределить find() и добавить byEmail или byUsername.
     * Я тут много что на ходу придумывал т.к.
     * конкретная реализация тут не имеет никакого значения
     *
     * @param $email
     * @return bool
     */
    public function existsByEmail($email)
    {
        return User::find()->andWhere([
            'email' => $email,
        ])->exists();
    }

    /**
     * @param $username
     * @return bool
     */
    public function existsByUsername($username)
    {
        return User::find()->andWhere([
            'username' => $username,
        ])->exists();
    }

    /**
     * @param string $identity
     * @return boolean
     */
    public function existsByUsernameOrEmail($identity)
    {
        return User::find()
            ->active()
            ->andWhere([
                'or',
                ['username' => $identity],
                ['email' => $identity]
            ])
            ->exists();
    }

    /**
     * @param string $identity
     * @return User|ActiveRecord
     */
    public function findByUsernameOrEmail($identity)
    {
        $user = User::find()
            ->active()
            ->andWhere([
                'or',
                ['username' => $identity],
                ['email' => $identity]
            ])
            ->one();
        if (null === $user) {
            throw new \InvalidArgumentException('Model not found');
        }

        return $user;
    }

    /**
     * @param $token
     * @param string $type
     * @return User|null|ActiveRecord
     */
    public function findByEmailAndTokenType($token, $type = UserToken::TYPE_ACTIVATION)
    {
        $user = User::find()
            ->joinWith(['userTokens'])
            ->andWhere([
                'user_token.token' => $token,
                'user_token.type' => $type
            ])
            ->andWhere(['>', 'user_token.expire_at', time()])
            ->one();

        if (null === $user) {
            throw new \InvalidArgumentException('Model not found');
        }

        return $user;
    }
}
