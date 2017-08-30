<?php

namespace luyatests\core\web;

use luya\web\JsonLd;
use Yii;

class JsonLdTest extends \luyatests\LuyaWebTestCase
{
    public function testAssignView()
    {
        Jsonld::addGraph(['foo' => 'bar']);

        ob_start();
        Yii::$app->view->beginBody();
        $out = ob_get_contents();
        ob_end_clean();

        $this->assertContains('<script type="application/ld+json">{"@graph":[{"foo":"bar"}]}</script>', $out);
    }
}
