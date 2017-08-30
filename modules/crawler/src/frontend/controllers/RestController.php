<?php

namespace luya\crawler\frontend\controllers;

use luya\crawler\models\Index;
use Yii;
use yii\helpers\Html;

class RestController extends \luya\rest\Controller
{
    public function userAuthClass()
    {
        return false;
    }

    public function actionIndex($query = null)
    {
        return [
            'query'   => Html::encode($query),
            'results' => Index::searchByQuery($query, Yii::$app->composition->getKey('langShortCode')),
        ];
    }
}
