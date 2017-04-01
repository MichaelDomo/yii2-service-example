<?php

namespace frontend\modules\user\controllers;

use frontend\modules\user\models\clients\ClientInterface;
use Yii;
use common\traits\AjaxValidationTrait;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\authclient\AuthAction;
use base\services\UserService;
use frontend\modules\user\forms\SignupForm;
use frontend\modules\user\forms\LoginForm;
use frontend\modules\user\forms\PasswordResetRequestForm;
use frontend\modules\user\forms\PasswordResetForm;
use frontend\modules\user\helpers\FlashHelper;

/**
 * Class SignController
 * @package frontend\modules\user\controllers
 */
class SignInController extends Controller
{
    use AjaxValidationTrait;

    private $userService;

    /**
     * SignController constructor.
     * @inheritdoc
     * @param UserService $userService
     */
    public function __construct($id, $module, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'oauth' => [
                'class' => AuthAction::class,
                'successCallback' => [$this, 'successOAuthCallback']
            ]
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'signup', 'login', 'request-password-reset', 'reset-password', 'oauth', 'confirm-signup'
                        ],
                        'allow' => true,
                        'roles' => ['?']
                    ],
                    [
                        'actions' => [
                            'signup', 'login', 'request-password-reset', 'reset-password', 'oauth', 'confirm-signup'
                        ],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function () {
                            return Yii::$app->controller->redirect(['/user/default/index']);
                        }
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post']
                ]
            ]
        ];
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionSignup()
    {
        $form = Yii::createObject(SignupForm::class);
        $this->performAjaxValidation($form);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->userService->requestSignup(
                $form->username,
                $form->email,
                $form->password
            );
            FlashHelper::createUserSignupFlash();
            return $this->redirect('/');
        }
        return $this->render('/sign-in/signup', [
            'model' => $form,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        $form = Yii::createObject(LoginForm::class);
        $this->performAjaxValidation($form);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->userService->requestLogin(
                $form->identity,
                $form->password,
                $form->rememberMe
            );
            return $this->redirect('/');
        }
        return $this->render('/sign-in/login', [
            'model' => $form,
        ]);
    }

    /**
     * @param $token
     * @return \yii\web\Response
     */
    public function actionConfirmSignup($token)
    {
        $this->userService->confirmSignup($token);
        FlashHelper::createUserConfirmSignupFlash();
        return $this->redirect('/');
    }

    /**
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        $this->userService->requestLogout();
        return $this->redirect('/');
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionRequestPasswordReset()
    {
        $form = Yii::createObject(PasswordResetRequestForm::class);
        $this->performAjaxValidation($form);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->userService->requestPasswordReset(
                $form->email
            );
            FlashHelper::createRequestPasswordResetSuccessFlash();
            return $this->redirect('/');
        }
        return $this->render('/sign-in/requestPasswordReset', [
            'model' => $form,
        ]);
    }

    /**
     * @param $token
     * @return string|\yii\web\Response
     */
    public function actionConfirmPasswordReset($token)
    {
        $form = new PasswordResetForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->userService->confirmPasswordReset(
                $token,
                $form->password
            );
            FlashHelper::createConfirmPasswordResetFlash();
            return $this->redirect('user/sign-in/login');
        }
        return $this->render('/sign-in/resetPassword', [
            'model' => $form,
        ]);
    }

    /**
     * @param $client ClientInterface
     * @return mixed
     */
    public function successOAuthCallback(ClientInterface $client)
    {
        try {
            $this->userService->oAuthSignup($client);
            FlashHelper::createAuthSuccessFlash();
            return $this->redirect('/');
        } catch (\Exception $e) {
            FlashHelper::createAuthErrorFlash();
            return $this->redirect('user/sign-in/signup');
        }
    }
}
