<?php

namespace luya\admin\controllers;

use luya\admin\base\Controller;
use Yii;

class AccountController extends Controller
{
    public $disablePermissionCheck = true;

    public function actionDashboard()
    {
        return $this->render('dashboard', [
            'user' => Yii::$app->adminuser->identity,
        ]);
    }
}
