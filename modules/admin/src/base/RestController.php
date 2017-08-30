<?php

namespace luya\admin\base;

use luya\rest\Controller;
use luya\rest\UserBehaviorInterface;
use Yii;

/**
 * provides the basic functionality to access and serialize this controller via rest
 * api. Does not define the method names!
 *
 * ´´´
 * class TestController extends \admin\base\RestController
 * {
 *     public function actionFooBar()
 *     {
 *         return ['foo', 'bar'];
 *     }
 * }
 *
 * @author Basil Suter <basil@nadar.io>
 */
class RestController extends Controller implements UserBehaviorInterface
{
    public function userAuthClass()
    {
        return Yii::$app->adminuser;
    }
}
