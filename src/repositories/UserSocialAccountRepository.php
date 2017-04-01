<?php

namespace base\repositories;

use common\models\UserSocialAccount;

/**
 * Class UserSocialAccountRepository
 * @package base\repositories
 */
class UserSocialAccountRepository
{
    /**
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws \InvalidArgumentException
     */
    public function find($id)
    {
        if (null === ($userSocialAccount = UserSocialAccount::findOne($id))) {
            throw new \InvalidArgumentException('Model not found');
        }

        return $userSocialAccount;
    }

    /**
     * @param UserSocialAccount $userSocialAccount
     * @throws \InvalidArgumentException
     */
    public function add(UserSocialAccount $userSocialAccount)
    {
        if (!$userSocialAccount->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $userSocialAccount->insert(false);
    }

    /**
     * @param UserSocialAccount $userSocialAccount
     * @throws \InvalidArgumentException
     */
    public function save(UserSocialAccount $userSocialAccount)
    {
        if ($userSocialAccount->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $userSocialAccount->update(false);
    }

    /**
     * @param $client
     * @return null|\yii\db\ActiveRecord
     */
    public function findByClient($client)
    {
        return UserSocialAccount::find()
            ->byClient($client)
            ->one();
    }
}
