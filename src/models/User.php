<?php

namespace common\models;

use Yii;
use common\models\query\UserQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * UserIdentity model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $email
 * @property string $auth_key
 * @property string $publicIdentity
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $logged_at
 *
 * @property UserProfile $userProfile
 * @property UserToken $userTokens
 * @property string $authKey
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DELETED = 3;

    const ROLE_USER = 'user';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADMINISTRATOR = 'administrator';

    const EVENT_AFTER_LOGIN = 'afterLogin';

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_NOT_ACTIVE => Yii::t('common', 'Not Active'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
            self::STATUS_DELETED => Yii::t('common', 'Deleted')
        ];
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $passwordHash
     * @param string $authKey
     * @param int $status
     * @return User
     */
    public static function requestSignup($username, $email, $passwordHash, $authKey, $status = self::STATUS_NOT_ACTIVE)
    {
        $user = new self();
        $user->username = $username;
        $user->email = $email;
        $user->status = $status;
        $user->password_hash = $passwordHash;
        $user->auth_key = $authKey;

        return $user;
    }

    /**
     * Confirm SignUp, set status active.
     */
    public function confirmSignup()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @param $passwordHash
     */
    public function changePassword($passwordHash)
    {
        $this->password_hash = $passwordHash;
    }

    /**
     * @return string
     */
    public function getPublicIdentity()
    {
        if ($this->userProfile && $this->userProfile->getFullName()) {
            return $this->userProfile->getFullName();
        }
        if ($this->username) {
            return $this->username;
        }

        return $this->email;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTokens()
    {
        return $this->hasMany(UserToken::class, ['user_id' => 'id']);
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            [['username', 'email'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::statuses())],
            [['username'], 'filter', 'filter' => '\yii\helpers\Html::encode']
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('common', 'Username'),
            'email' => Yii::t('common', 'E-mail'),
            'status' => Yii::t('common', 'Status'),
            'access_token' => Yii::t('common', 'API access token'),
            'created_at' => Yii::t('common', 'Created at'),
            'updated_at' => Yii::t('common', 'Updated at'),
            'logged_at' => Yii::t('common', 'Last login'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->joinWith(['userTokens'])
            ->active()
            ->andWhere(['user_token.token' => $token])
            ->andWhere(['user_token.type' => UserToken::TYPE_API])
            ->andWhere(['>', 'user_token.expire_at', time()])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->active()
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Connected accounts ($provider => $account)
     *
     * @return UserSocialAccount[]
     */
    //public function getAccounts()
    //{
    //    $connected = [];
    //    $accounts  = $this->hasMany(UserSocialAccount::class, ['user_id' => 'id'])->all();
    //    /** @var UserSocialAccount $account */
    //    foreach ($accounts as $account) {
    //        $connected[$account->provider] = $account;
    //    }
    //    return $connected;
    //}

    /**
     * Returns connected account by provider.
     *
     * @param  string $provider
     * @return UserSocialAccount|null
     */
    //public function getAccountByProvider($provider)
    //{
    //    $accounts = $this->getAccounts();
    //    return isset($accounts[$provider])
    //        ? $accounts[$provider]
    //        : null;
    //}
}
