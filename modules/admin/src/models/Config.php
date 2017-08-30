<?php

namespace luya\admin\models;

use luya\traits\RegistryTrait;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "admin_config".
 *
 * @property string $name
 * @property string $value
 *
 * @author Basil Suter <basil@nadar.io>
 */
final class Config extends ActiveRecord
{
    use RegistryTrait;

    const CONFIG_LAST_IMPORT_TIMESTAMP = 'last_import_timestamp';

    const CONFIG_SETUP_COMMAND_TIMESTAMP = 'setup_command_timestamp';

    const CONFIG_INSTALLER_VENDOR_TIMESTAMP = 'installer_vendor_timestamp';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name'  => 'Name',
            'value' => 'Value',
        ];
    }
}
