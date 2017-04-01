<?php

namespace base\repositories;

use common\models\UserProfile;

/**
 * Class UserProfileRepository
 * @package base\repositories
 */
class UserProfileRepository
{
    /**
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws \InvalidArgumentException
     */
    public function find($id)
    {
        if (null === ($userProfile = UserProfile::findOne($id))) {
            throw new \InvalidArgumentException('Model not found');
        }

        return $userProfile;
    }

    /**
     * @param UserProfile $userProfile
     * @throws \InvalidArgumentException
     */
    public function add(UserProfile $userProfile)
    {
        if (!$userProfile->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $userProfile->insert(false);
    }

    /**
     * @param UserProfile $userProfile
     * @throws \InvalidArgumentException
     */
    public function save(UserProfile $userProfile)
    {
        if ($userProfile->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $userProfile->update(false);
    }
}
