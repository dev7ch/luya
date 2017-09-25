<?php

namespace luya\cms\frontend\commands;

use Yii;
use yii\helpers\Inflector;
use yii\helpers\Console;
use luya\helpers\StringHelper;
use luya\helpers\FileHelper;

/**
 * Block console commands.
 *
 * @property string $blockName The name of the block getter/setters stored.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class BlockController extends \luya\console\Command
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'create';

    /**
     * @var string Type module
     */
    const TYPE_MODULE = 'module';

    /**
     * @var string Type application block
     */
    const TYPE_APP = 'app';

    /**
     * @var string The type of block, valid `app` (static::TYPE_APP) or `module` (static::TYPE_TMODULE) values.
     */
    public $type;

    /**
     * @var string If type is `module` the name of the module must be provided with this $moduleName property.
     */
    public $moduleName;

    /**
     * @var array Provide the configuration array which is inside the `config()` method of the block.
     */
    public $config;

    /**
     * @var boolean Whether the block is a container/layout block or not this will enable/dsiable the $isContainer property
     */
    public $isContainer;

    /**
     * @var boolean Whether the caching property should be displayed or not inside the block.
     */
    public $cacheEnabled;

    /**
     * @var boolean If dry run is enabled the content of the block will be returned but no files will be created. This is usefull for unit testing.
     */
    public $dryRun = false;

    private $_blockName;

    /**
     * Setter method for $blockName, ensure the correct block name.
     *
     * @param string $name The name of the block.
     */
    public function setBlockName($name)
    {
        if (!StringHelper::endsWith($name, 'Block')) {
            $name .= 'Block';
        }

        $this->_blockName = Inflector::camelize($name);
    }

    /**
     * Getter method fro $blockName.
     *
     * @return string Get the ensured block name.
     */
    public function getBlockName()
    {
        return $this->_blockName;
    }

    /**
     * @var array An array with the list of extras which are generated during the var creator process, example content `'foo' => 'value',`
     */
    public $extras = [];

    /**
     * @var array An array with all phpdoc comments which should be added to the admin template, exmaple content `['{{extras.foobar}}']`.
     */
    public $phpdoc = [];

    /**
     * @var array Am array with additional docblocks messages to render inside the view file.
     */
    public $viewFileDoc = [];

    /**
     * Get an array with all modules where you can generate blocks for.
     *
     * @return array
     */
    private function getModuleProposal()
    {
        $moduleNames = [];
        foreach (Yii::$app->getApplicationModules() as $id => $obj) {
            $moduleNames[$id] = $id;
        }

        return $moduleNames;
    }

    private function getVariableTypes()
    {
        $variableTypes = [
            '0' => 'text - Textinput',
            '1' => 'textarea - Textarea multi rows input',
            '2' => 'password - Passwort input field (hides the signs)',
            '3' => 'number - Numbers allowed only',
            '4' => 'decimal - Decimal Number Float',
            '5' => 'wysiwyg - What you see is what you get Editor',
            '6' => 'select - Dropdown Select',
            //'double entry ? key 20 ?' => 'Radios Selection',

            '7' => 'date - Date Selector',
            '8' => 'datetime - Date and Time selector',
            '9' => 'checkbox - A single Checkbox',
            '10' => 'checkbox-array - radio Buttons with several inputs',
            '11' => 'file-upload - User can upload a single file',
            '12' => 'file-array-upload - User can upload severals files',
            '13' => 'image-upload - creata a image upload form and return the imageId on success',
            '14' => 'image-array-upload - creates an array with image id an caption string',
            '15' => 'list-array - Creates an array with a key variable value',
            '16' => 'table - User can dynamic create tables (jsons)',
            '17' => 'link - Generates a linkable internal or external resource (use Link Injector!)',
            '18' => 'cms-page - Returns CMS page selection tree (only when cms is registered).',
            '19' => 'slug - Slugified input field which allows only lower chars and - for url rules.',
            '20' => 'radio - Generate radio inputs which allows to select and return a single value.',
            '21' => 'multiple-inputs - Nesting all types inside an array.'
        ];

        foreach (array_values($variableTypes) as $i => $var) {

            echo "[$i] - $var\n";
        }

        return $variableTypes;

    }

    private function getVariableTypeInterfaceMap()
    {
        return [
            '0' => 'self::TYPE_TEXT',
            '1' => 'self::TYPE_TEXTAREA',
            '2' => 'self::TYPE_PASSWORD',
            '3' => 'self::TYPE_NUMBER',
            '4' => 'self::TYPE_DECIMAL',
            '5' => 'self::TYPE_WYSIWYG',
            '6' => 'self::TYPE_SELECT',
            '7' => 'self::TYPE_DATE',
            '8' => 'self::TYPE_DATETIME',
            '9' => 'self::TYPE_CHECKBOX',
            '10' => 'self::TYPE_CHECKBOX_ARRAY',
            '11' => 'self::TYPE_FILEUPLOAD',
            '12' => 'self::TYPE_FILEUPLOAD_ARRAY',
            '13' => 'self::TYPE_IMAGEUPLOAD',
            '14' => 'self::TYPE_IMAGEUPLOAD_ARRAY',
            '15' => 'self::TYPE_LIST_ARRAY',
            '16' => 'self::TYPE_TABLE',
            '17' => 'self::TYPE_LINK',
            '18' => 'self::TYPE_CMS_PAGE',
            '19' => 'self::TYPE_SLUG',
            '20' => 'self::TYPE_RADIO',
            '21' => 'self::TYPE_MULTIPLE_INPUTS',
        ];
    }

    private function getVariableTypesOptions()
    {
        return [
            'select' => "BlockHelper::selectArrayOption([1 => 'Label for 1'])",
            'radio' => "BlockHelper::selectArrayOption([1 => 'Label for 1'])",
            'checkbox-array' => "BlockHelper::checkboxArrayOption([1 => 'Label for 1'])",
            'image-upload' => "['no_filter' => false]",
            'image-array-upload' => "['no_filter' => false]",
            'radio' => "BlockHelper::radioArrayOption([1 => 'Label for 1'])",
        ];
    }

    private function getExtraVarDef($type, $varName, $func)
    {
        $info = [
            'image-upload' => function ($varName) use ($func) {
                return 'BlockHelper::imageUpload($this->'.$func.'(\''.$varName.'\'), false, true),';
            },
            'image-array-upload' => function ($varName) use ($func) {
                return 'BlockHelper::imageArrayUpload($this->'.$func.'(\''.$varName.'\'), false, true),';
            },
            'file-upload' => function ($varName) use ($func) {
                return 'BlockHelper::fileUpload($this->'.$func.'(\''.$varName.'\'), true),';
            },
            'file-array-upload' => function ($varName) use ($func) {
                return 'BlockHelper::fileArrayUpload($this->'.$func.'(\''.$varName.'\'), true),';
            },
            'cms-page' => function ($varName) use ($func) {
                return 'Yii::$app->menu->findOne([\'nav_id\' => $this->'.$func.'(\''.$varName.'\', 0)]),';
            },
        ];

        if (array_key_exists($type, $info)) {
            return "'".$varName."' => ".$info[$type]($varName);
        }

        return false;
    }

    private function getVariableTypeOption($type)
    {
        $types = $this->getVariableTypesOptions();

        return $types[$type];
    }

    private function hasVariableTypeOption($type)
    {
        return array_key_exists($type, $this->getVariableTypesOptions());
    }

    private function placeholderCreator($prefix)
    {
        $this->output(PHP_EOL.'-> Create new '.$prefix, Console::FG_YELLOW);
        $name = $this->prompt('Variable Name:', ['required' => true]);
        $label = $this->prompt('End-User Label:', ['required' => true]);

        $v = [
            'var' => Inflector::variablize($name),
            'label' => $label,
        ];

        $this->output('Added '.$prefix.PHP_EOL, Console::FG_GREEN);

        return $v;
    }

    /**
     * Create a variable based of user input.
     *
     * @param string $prefix
     * @param string $typeCast 'var', 'cfg'
     * @return array
     */
    private function varCreator($prefix, $typeCast)
    {
        $this->output(PHP_EOL.'-> Create new '.$prefix, Console::FG_YELLOW);
        $name = $this->prompt('Variable Name:', ['required' => true]);
        $label = $this->prompt('End-User Label:', ['required' => true]);
        $type = $this->select('Variable Type:', $this->getVariableTypes());

        $v = [
            'var' => Inflector::variablize($name),
            'label' => $label,
            'type' => $this->getVariableTypeInterfaceMap()[$type],
        ];

        if ($this->hasVariableTypeOption($type)) {
            $v['options'] = $this->getVariableTypeOption($type);
        }

        if ($typeCast == 'var') {
            $func = 'getVarValue';
        } else {
            $func = 'getCfgValue';
        }

        $extra = $this->getExtraVarDef($type, $v['var'], $func);

        if ($extra !== false) {
            $this->phpdoc[] = '{{extras.'.$v['var'].'}}';
            $this->viewFileDoc[] = '$this->extraValue(\''.$v['var'].'\');';
            $this->extras[] = $extra;
        }

        $this->output('Added '.$prefix.PHP_EOL, Console::FG_GREEN);

        return $v;
    }

    /**
     * Get the file namespace based on its type.
     *
     * @return string The full qualified namespace based on the type
     */
    protected function getFileNamespace()
    {
        if ($this->type == self::TYPE_APP) {
            return 'app\\blocks';
        }

        return Yii::$app->getModule($this->moduleName)->getNamespace()  . '\\blocks';
    }

    /**
     * Get the full base path to the folder of the module
     *
     * @return string The full path to the module folder.
     */
    protected function getFileBasePath()
    {
        if ($this->type == self::TYPE_APP) {
            return Yii::$app->basePath;
        }

        return Yii::$app->getModule($this->moduleName)->getBasePath();
    }

    /**
     * Generate the view file for the block.
     *
     * @param string $blockClassName The name of the block class.
     * @return string The rendered view file.
     */
    public function generateViewFile($blockClassName)
    {
        sort($this->viewFileDoc);
        return $this->view->render('@cms/views/commands/block/create_block_view.php', [
            'blockClassName' => $blockClassName,
            'phpdoc' => $this->viewFileDoc,
            'luyaText' => $this->getGeneratorText('block/create'),
        ]);
    }

    /**
     * Wizzard to create a new CMS block.
     *
     * @return number
     */
    public function actionCreate()
    {
        if (empty($this->type)) {
            Console::clearScreenBeforeCursor();
            $this->type = $this->select('Do you want to create an app or module Block?', [
                self::TYPE_APP => 'Creates a project block inside your @app Namespace (casual).',
                self::TYPE_MODULE => 'Creating a block inside a later specified Module.',
            ]);
        }

        if ($this->type == self::TYPE_MODULE && count($this->getModuleProposal()) === 0) {
            return $this->outputError('Your project does not have Project-Modules registered!');
        }

        if (empty($this->moduleName) && $this->type == self::TYPE_MODULE) {
            $this->moduleName = $this->select('Choose a module to create the block inside:', $this->getModuleProposal());
        }

        if (empty($this->blockName)) {
            $this->blockName = $this->prompt('Insert a name for your Block (e.g. HeadTeaser):', ['required' => true]);
        }

        if ($this->isContainer === null) {
            $this->isContainer = $this->confirm("Do you want to add placeholders to your block that serve as a container for nested blocks?", false);
        }

        if ($this->cacheEnabled === null) {
            $this->cacheEnabled = $this->confirm("Do you want to enable the caching for this block or not?", true);
        }

        if ($this->config === null) {
            $this->config = [
                'vars' => [], 'cfgs' => [], 'placeholders' => [],
            ];

            $doConfigure = $this->confirm('Would you like to configure this Block? (vars, cfgs, placeholders)', false);

            if ($doConfigure) {
                $doVars = $this->confirm('Add new Variable (vars)?', false);
                $i = 1;
                while ($doVars) {
                    $item = $this->varCreator('Variabel (vars) #'.$i, 'var');
                    $this->phpdoc[] = '{{vars.'.$item['var'].'}}';
                    $this->viewFileDoc[] = '$this->varValue(\''.$item['var'].'\');';
                    $this->config['vars'][] = $item;
                    $doVars = $this->confirm('Add one more?', false);
                    ++$i;
                }
                $doCfgs = $this->confirm('Add new Configuration (cfgs)?', false);
                $i = 1;
                while ($doCfgs) {
                    $item = $this->varCreator('Configration (cfgs) #'.$i, 'cfg');
                    $this->phpdoc[] = '{{cfgs.'.$item['var'].'}}';
                    $this->viewFileDoc[] = '$this->cfgValue(\''.$item['var'].'\');';
                    $this->config['cfgs'][] = $item;
                    $doCfgs = $this->confirm('Add one more?', false);
                    ++$i;
                }
                $doPlaceholders = $this->confirm('Add new Placeholder (placeholders)?', false);
                $i = 1;
                while ($doPlaceholders) {
                    $item = $this->placeholderCreator('Placeholder (placeholders) #'.$i);
                    $this->phpdoc[] = '{{placeholders.'.$item['var'].'}}';
                    $this->viewFileDoc[] = '$this->placeholderValue(\''.$item['var'].'\');';
                    $this->config['placeholders'][] = $item;
                    $doPlaceholders = $this->confirm('Add one more?', false);
                    ++$i;
                }
            }
        }

        $folder = $this->getFileBasePath() . DIRECTORY_SEPARATOR . 'blocks';
        $filePath = $folder . DIRECTORY_SEPARATOR . $this->blockName . '.php';

        sort($this->phpdoc);

        $content = $this->view->render('@cms/views/commands/block/create_block', [
            'namespace' => $this->getFileNamespace(),
            'className' => $this->blockName,
            'name' => Inflector::camel2words($this->blockName),
            'type' => $this->type,
            'module' => $this->moduleName,
            'isContainer' => $this->isContainer,
            'cacheEnabled' => $this->cacheEnabled,
            'config' => $this->config,
            'phpdoc' => $this->phpdoc,
            'extras' => $this->extras,
            'luyaText' => $this->getGeneratorText('block/create'),
        ]);

        if ($this->dryRun) {
            return $content;
        }

        if (FileHelper::createDirectory($folder) && FileHelper::writeFile($filePath, $content)) {

            // generate view file based on block object view context
            $object = Yii::createObject(['class' => $this->getFileNamespace() . '\\' . $this->blockName]);
            $viewsFolder = Yii::getAlias($object->getViewPath());
            $viewFilePath = $viewsFolder . DIRECTORY_SEPARATOR . $object->getViewFileName('php');
            if (FileHelper::createDirectory($viewsFolder) && FileHelper::writeFile($viewFilePath, $this->generateViewFile($this->blockName))) {
                $this->outputInfo('View file for the block has been created: ' . $viewFilePath);
            }

            return $this->outputSuccess("Block {$this->blockName} has been created: " . $filePath);
        }

        return $this->outputError("Error while creating block '$filePath'");
    }
}
