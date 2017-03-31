<?php

namespace michaeldomo\service\controllers;

use Yii;
use yii\base\Controller;
use michaeldomo\service\services\UserService;
use michaeldomo\service\forms\SignupForm;

class SignController extends Controller
{
    private $userService;

    public function __construct($id, $module, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $module, $config);
    }

    public function actionSignup()
    {
        $form = Yii::createObject(SignupForm::class);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->userService->requestSignup(
                $form->username,
                $form->email,
                $form->password
            );
            Yii::$app->session->setFlash('success', 'Please confirm your Email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $form,
        ]);
    }

    public function actionConfirmSignup($token)
    {
        $this->userService->confirmSignup($token);
        Yii::$app->getSession()->setFlash('success', 'Email is confirmed successfully.');
        return $this->goHome();
    }
}
