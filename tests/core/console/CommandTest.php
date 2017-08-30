<?php

namespace luyatests\core\console;

use luya\console\Command;
use luyatests\LuyaConsoleTestCase;
use Yii;

class SubCommand extends Command
{
    // as command is an abstract class
}

class CommandTest extends LuyaConsoleTestCase
{
    public function testModuleTypeSelector()
    {
        $cmd = new SubCommand('myid', Yii::$app->getModule('unitmodule'));

        $className = $cmd->createClassName('das-ist-mein', 'mein0r');

        $this->assertEquals('FooActiveWindow', $cmd->createClassName('FooActiveWindow', 'ActiveWindow'));
        $this->assertEquals('FooActiveWindow', $cmd->createClassName('foo-active-window', 'ActiveWindow'));
        $this->assertEquals('FooActiveWindow', $cmd->createClassName('foo', 'ActiveWindow'));

        $this->assertEquals(\luya\Boot::VERSION, $cmd->getLuyaVersion());
    }

    public function testHelper()
    {
        $cmd = new SubCommand('myid', Yii::$app->getModule('unitmodule'));

        $this->assertEquals(\luya\Boot::VERSION, $cmd->getLuyaVersion());
    }
}
