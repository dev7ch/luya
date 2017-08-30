<?php

namespace luya\cms\models;

use luya\admin\ngrest\base\NgRestModel;
use luya\admin\traits\SoftDeleteTrait;

/**
 * Represents the Block-Group Model where blocks can be stored inside.
 *
 * @author Basil Suter <basil@nadar.io>
 */
class BlockGroup extends NgRestModel
{
    use SoftDeleteTrait;

    public static function ngRestApiEndpoint()
    {
        return 'api-cms-blockgroup';
    }

    public static function tableName()
    {
        return 'cms_block_group';
    }

    public function attributeLabels()
    {
        return [
            'name'      => 'Name',
            'identifer' => 'Identifier',
        ];
    }

    public function ngRestAttributeTypes()
    {
        return [
            'name'       => 'text',
            'identifier' => 'text',
        ];
    }

    public function ngRestConfig($config)
    {
        $this->ngRestConfigDefine($config, ['list', 'create', 'update'], ['name', 'identifier']);

        return $config;
    }

    public function rules()
    {
        return [
            [['name', 'identifier'], 'required'],
            ['identifier', 'unique'],
        ];
    }

    public function scenarios()
    {
        return [
            'restcreate' => ['name', 'identifier'],
            'restupdate' => ['name', 'identifier'],
        ];
    }
}
