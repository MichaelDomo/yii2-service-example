<?php

namespace base\repositories;

use common\models\UserToken;
use yii\db\ActiveRecord;

/**
 * Class UserTokenRepository
 * @package base\repositories
 */
class UserTokenRepository
{
    /**
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws \InvalidArgumentException
     */
    public function find($id)
    {
        if (null === ($userToken = UserToken::findOne($id))) {
            throw new \InvalidArgumentException('Model not found');
        }

        return $userToken;
    }

    /**
     * @param UserToken $userToken
     * @throws \InvalidArgumentException
     */
    public function add(UserToken $userToken)
    {
        if (!$userToken->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $userToken->insert(false);
    }

    /**
     * @param UserToken $userToken
     * @throws \InvalidArgumentException
     */
    public function save(UserToken $userToken)
    {
        if ($userToken->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $userToken->update(false);
    }

    /**
     * @param UserToken $userToken
     * @throws \InvalidArgumentException
     */
    public function delete(UserToken $userToken)
    {
        if ($userToken->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $userToken->delete();
    }

    /**
     * @param $token
     * @param $type
     * @return UserToken|ActiveRecord
     */
    public function findActiveTokenByTokenAndType($token, $type = UserToken::TYPE_ACTIVATION)
    {
        $userToken = UserToken::find()
            ->byType($type)
            ->byToken($token)
            ->notExpired()
            ->one();

        if (null === $userToken) {
            throw new \InvalidArgumentException('Model not found');
        }

        return $userToken;
    }
}
