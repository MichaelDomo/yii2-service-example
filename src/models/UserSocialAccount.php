<?php

namespace common\models;

use common\models\query\UserSocialAccountQuery;
use frontend\modules\user\models\clients\ClientInterface;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_social_account}}".
 *
 * TODO refactor, it must be only AR, UserSocialAccountService
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $provider
 * @property string $client_id
 * @property integer $created_at
 * @property string $email
 * @property string $username
 * @property string $data
 *
 * @property mixed $decodedData
 * @property User $user
 */
class UserSocialAccount extends ActiveRecord
{
    /**
     * @var array|null
     */
    private $_data;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_social_account}}';
    }

    /**
     * @param ClientInterface $client
     * @return UserSocialAccount
     */
    public static function create(ClientInterface $client)
    {
        $userSocialAccount = new self();
        $userSocialAccount->provider = $client->getId();
        $userSocialAccount->client_id = $client->getUserId();
        $userSocialAccount->username = $client->getUsername();
        $userSocialAccount->email = $client->getEmail();
        $userSocialAccount->data = json_encode($client->getUserAttributes());

        return $userSocialAccount;
    }

    /**
     * @param $userId
     */
    public function bindUser($userId)
    {
        $this->user_id = $userId;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return mixed Json decoded properties.
     */
    public function getDecodedData()
    {
        if ($this->_data === null) {
            $this->_data = Json::decode($this->data, true);
        }
        return $this->_data;
    }


    /**
     * @return UserSocialAccountQuery
     */
    public static function find()
    {
        return new UserSocialAccountQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provider', 'client_id'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['data'], 'string'],
            [['provider', 'client_id', 'email', 'username'], 'string', 'max' => 255],
            [['provider', 'client_id'],
                'unique',
                'targetAttribute' => ['provider', 'client_id'],
                'message' => Yii::t('common', 'The combination of Provider and Client ID has already been taken.')
            ],
            [['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updateAtAttribute' => false,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'user_id' => Yii::t('common', 'User ID'),
            'provider' => Yii::t('common', 'Provider'),
            'client_id' => Yii::t('common', 'Client ID'),
            'data' => Yii::t('common', 'Data'),
            'created_at' => Yii::t('common', 'Created At'),
            'email' => Yii::t('common', 'Email'),
            'username' => Yii::t('common', 'Username'),
        ];
    }
}
