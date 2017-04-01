<?php

namespace frontend\modules\user\forms;

use yii\base\Model;
use Yii;
use yii\web\JsExpression;
use common\models\User;

/**
 * Class AccountForm
 * @package frontend\modules\user\models
 *
 * @property \common\models\User $user
 */
class AccountForm extends Model
{
    /**
     * @var string $username
     */
    public $username;
    /**
     * @var string $email
     */
    public $email;
    /**
     * @var string $password
     */
    public $password;
    /**
     * @var string $password_confirm
     */
    public $password_confirm;
    /**
     * @var User $_user
     */
    private $_user;

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
        $this->email = $user->email;
        $this->username = $user->username;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique',
                'targetClass' => User::class,
                'message' => Yii::t('frontend', 'This username has already been taken.'),
                'filter' => function ($query) {
                    /* @var $query \yii\db\Query */
                    $query->andWhere(['not', ['id' => $this->getUser()->getId()]]);
                }
            ],
            ['username', 'string', 'min' => 1, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => User::class,
                'message' => Yii::t('frontend', 'This email has already been taken.'),
                'filter' => function ($query) {
                    /* @var $query \yii\db\Query */
                    $query->andWhere(['not', ['id' => $this->getUser()->getId()]]);
                }
            ],

            [['password', 'password_confirm'], 'filter', 'filter' => 'trim'],
            ['password', 'string'],
            [
                'password_confirm',
                'required',
                'when' => function ($model) {
                    return !empty($model->password);
                },
                'whenClient' => new JsExpression("function (attribute, value) {
                    return $('#accountform-password').val().length > 0;
                }")
            ],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('frontend', 'Username'),
            'email' => Yii::t('frontend', 'Email'),
            'password' => Yii::t('frontend', 'Password'),
            'password_confirm' => Yii::t('frontend', 'Confirm Password')
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (null !== ($user = $this->getUser())) {
            $user->username = $this->username;
            $user->email = $this->email;
            if ($this->password) {
                $this->getUser()->setPassword($this->password);
            }

            return $user->save();
        }

        return null;
    }
}
