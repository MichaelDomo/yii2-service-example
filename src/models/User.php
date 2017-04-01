<?php

namespace michaeldomo\service\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use michaeldomo\service\services\AuthTokenizer;
use michaeldomo\service\services\PasswordHasher;

/**
 * Class User
 * @package michaeldomo\service\models
 *
 * @property $status integer
 * @property $username integer
 * @property string $password_hash
 * @property $email integer
 * @property mixed $id
 * @property string $auth_key
 * @property string $email_confirm_token
 * @property string $password_reset_token
 *
 * @property string $authKey
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_BLOCK = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 2;

    public static function tableName()
    {
        return 'user';
    }

    public static function requestSignup($username, $email, $password, PasswordHasher $passwordHasher, AuthTokenizer $authTokenizer)
    {
        $user = new self();
        $user->username = $username;
        $user->email = $email;
        $user->status = self::STATUS_NOT_ACTIVE;
        $user->password_hash = $passwordHasher->hash($password);
        $user->email_confirm_token = $authTokenizer->generate();
        $user->changeEmailConfirmToken($authTokenizer);
        $user->changePassword($passwordHasher, $password);

        return $user;
    }

    public function confirmSignup()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * Только для того чтобы не ругался интерфейс
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->andWhere(['access_token' => $token])
            ->one();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function findIdentity($id)
    {
        return static::find()
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->andWhere(['id' => $id])
            ->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }
}
