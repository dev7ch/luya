<?php

namespace luyatests\core\console\commands;

use luya\console\commands\HealthController;
use Yii;

class HealthControllerTest extends \luyatests\LuyaConsoleTestCase
{
    public function testActionIndex()
    {
        $ctrl = new HealthController('ctrl', Yii::$app);
        $ctrl->actionIndex();
    }
}
