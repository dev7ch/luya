<?php

namespace luyatests\core\console;

use luya\console\commands\ImportController;
use luya\console\Importer;
use luyatests\LuyaConsoleTestCase;
use Yii;

class StubImporter extends Importer
{
    public function run()
    {
        return $this->importer->id;
    }
}

class ImporterTest extends LuyaConsoleTestCase
{
    public function testInstance()
    {
        $importRunner = new ImportController('import-runner', Yii::$app);

        $import = new StubImporter($importRunner);
        $this->assertSame('import-runner', $import->run());

        $import->addLog('value');

        $this->assertArrayHasKey('luyatests\core\console\StubImporter', $importRunner->getLog());
    }
}
