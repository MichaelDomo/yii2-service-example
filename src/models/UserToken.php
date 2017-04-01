<?php

namespace common\models;

use Yii;
use base\services\interfaces\AuthTokenizerInterface;
use common\models\query\UserTokenQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_token}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $token
 * @property integer $expire_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class UserToken extends ActiveRecord
{
    const TYPE_API = 'api';
    const TYPE_ACTIVATION = 'activation';
    const TYPE_PASSWORD_RESET = 'password_reset';

    /**
     * @param integer $user_id
     * @param string $type
     * @param AuthTokenizerInterface $authTokenizer
     * @param null|integer $duration
     * @return UserToken
     */
    public static function create($user_id, AuthTokenizerInterface $authTokenizer, $type = null, $duration = null)
    {
        $userToken = new self;

        $userToken->user_id = $user_id;
        $userToken->type = $type ?: self::TYPE_ACTIVATION;
        $userToken->expire_at = $duration ? time() + $duration : null;
        $userToken->token = $authTokenizer->generate();

        return $userToken;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_token}}';
    }

    /**
     * @return UserTokenQuery
     */
    public static function find()
    {
        return new UserTokenQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'token'], 'required'],
            [['user_id', 'expire_at'], 'integer'],
            [['type', 'token'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class
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
            'type' => Yii::t('common', 'Type'),
            'token' => Yii::t('common', 'Token'),
            'expire_at' => Yii::t('common', 'Expire At'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }
}
