<?php

namespace luya\admin\folder;

use luya\admin\storage\ItemAbstract;
use Yii;

/**
 * Storage Folder Item.
 *
 * @property int $id The unique folder id.
 * @property string $name The name of the Folder.
 * @property int $parentId The id of the parent folder.
 * @property int filesCount The number of files inside the folder.
 *
 * @author Basil Suter <basil@nadar.io>
 */
class Item extends ItemAbstract
{
    /**
     * The unique folder id.
     *
     * @return int The folder ID.
     */
    public function getId()
    {
        return (int) $this->itemArray['id'];
    }

    /**
     * The name of the Folder.
     *
     * @return string Returns the name of the Folder.
     */
    public function getName()
    {
        return $this->itemArray['name'];
    }

    /**
     * The parent folder Id.
     *
     * If not parent folder exists the value is `0`.
     *
     * @return int The parent folders id, 0 if no parent exists.
     */
    public function getParentId()
    {
        return (int) $this->itemArray['parent_id'];
    }

    /**
     * Whether the current folder has a parent or not.
     *
     * @return bool Whether a parent folder exists or is root node (false).
     */
    public function hasParent()
    {
        return !empty($this->getParentId());
    }

    /**
     * Whether the current folder has at least one child folder.
     *
     * @return bool Whether a child folder exists or not.
     */
    public function hasChild()
    {
        return (new \luya\admin\folder\Query())->where(['is_deleted' => 0, 'parent_id' => $this->getId()])->count() > 0 ? true : false;
    }

    /**
     * Get the parent folder object.
     *
     * @return bool|\luya\admin\folder\Item The item object or false if not found.
     */
    public function getParent()
    {
        return (!empty($this->getParentId())) ? Yii::$app->storage->getFolder($this->getParentId()) : false;
    }

    /**
     * The number of files inside the Current Folder.
     *
     * @return int
     */
    public function getFilesCount()
    {
        return (int) (new \luya\admin\file\Query())->where(['is_hidden' => 0, 'is_deleted' => 0, 'folder_id' => $this->getId()])->count();
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return ['id', 'name', 'parentId', 'filesCount'];
    }
}
