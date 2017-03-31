<?php

namespace michaeldomo\service\repositories;

use michaeldomo\service\models\User;

class UserRepository
{
    public function find($id)
    {
        if (!$user = User::findOne($id)) {
            throw new \InvalidArgumentException('Model not found');
        }
        return $user;
    }

    /**
     * В User можно переопределить find() и добавить byEmail или byUsername.
     * Я тут много что на ходу придумывал т.к.
     * конкретная реализация тут не имеет никакого значения
     */
    public function existsByEmail($email)
    {
        return User::find()->andWhere([
            'email' => $email,
        ])->exists();
    }

    public function existsByUsername($username)
    {
        return User::find()->andWhere([
            'username' => $username,
        ])->exists();
    }

    public function findByEmailConfirmToken($token)
    {
        return User::find()
            ->andWhere(['like', 'email_confirm_token', $token])
            ->one();
    }

    public function add(User $user)
    {
        if (!$user->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $user->insert(false);
    }

    public function save(User $user)
    {
        if (!$user->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $user->update(false);
    }
}
