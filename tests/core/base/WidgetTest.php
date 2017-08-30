<?php

namespace luyatests\core;

use luya\base\Widget;
use luyatests\LuyaWebTestCase;

class WidgetTest extends LuyaWebTestCase
{
    public function testOriginalViewPath()
    {
        $this->assertContains('core/base/views', (new Widget())->getViewPath());
    }

    public function testUseAppViewsPath()
    {
        $this->assertContains('@app/views/widgets', (new Widget(['useAppViewPath' => true]))->getViewPath());
    }
}
