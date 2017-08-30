<?php

namespace luyatests\core\web;

use luya\web\ErrorHandler;
use luyatests\LuyaWebTestCase;
use Yii;
use yii\web\NotFoundHttpException;

class ErrorHandlerTest extends LuyaWebTestCase
{
    public function testTriggerException()
    {
        $handler = new ErrorHandler();
        $exception = new NotFoundHttpException('Whoops');

        ob_start();
        $handler->renderException($exception);
        ob_end_clean();

        $this->assertContains('Whoops', Yii::$app->response->data);
    }
}
