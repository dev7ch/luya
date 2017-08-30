<?php

namespace luya\admin\ngrest\base\actions;

use luya\admin\models\UserOnline;
use Yii;

/**
 * UpdateAction for REST implementation.
 *
 * @author Basil Suter <basil@nadar.io>
 */
class UpdateAction extends \yii\rest\UpdateAction
{
    public function run($id)
    {
        $response = parent::run($id);

        UserOnline::unlock(Yii::$app->adminuser->id);

        return $response;
    }
}
