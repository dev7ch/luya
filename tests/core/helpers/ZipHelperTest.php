<?php

namespace luyatests\core\helpers;

use luya\helpers\ZipHelper;
use luyatests\LuyaWebTestCase;
use Yii;

class ZipHelperTest extends LuyaWebTestCase
{
    public function testZipDir()
    {
        ZipHelper::dir(__DIR__, Yii::getAlias('@runtime/test.zip'));
    }
}
