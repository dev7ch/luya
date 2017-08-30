<?php

namespace luya\admin\ngrest\render;

use luya\admin\ngrest\base\Render;
use Yii;

/**
 * Render the index view of an Active Window.
 *
 * @property \luya\admin\ngrest\base\ActiveWindowInterface $activeWindowObject
 * @property int $itemId
 * @property string $activeWindowHash
 *
 * @author Basil Suter <basil@nadar.io>
 *
 * @since 1.0.0
 */
class RenderActiveWindow extends Render implements RenderInterface
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->getActiveWindowObject()->index();
    }

    private $_activeWindowObject;

    /**
     * Get current Active Window Object.
     *
     * @return \luya\admin\ngrest\base\ActiveWindowInterface
     */
    public function getActiveWindowObject()
    {
        if ($this->_activeWindowObject === null) {
            $activeWindow = $this->findActiveWindow($this->activeWindowHash);
            $object = Yii::createObject($activeWindow['objectConfig']);
            $object->setItemId($this->_itemId);
            $object->setConfigHash($this->config->getHash());
            $object->setActiveWindowHash($this->activeWindowHash);
            Yii::$app->session->set($this->activeWindowHash, $this->_itemId);
            $this->_activeWindowObject = $object;
        }

        return $this->_activeWindowObject;
    }

    private $_activeWindowHash;

    /**
     * Active Window Hash Getter Method.
     *
     * @return string
     */
    public function getActiveWindowHash()
    {
        return $this->_activeWindowHash;
    }

    /**
     * Active Window Hash Setter Method.
     *
     * @param string $activeWindowHash
     */
    public function setActiveWindowHash($activeWindowHash)
    {
        $this->_activeWindowHash = $activeWindowHash;
    }

    private $_itemId;

    /**
     * Setter Method for current Item Id.
     *
     * @param int $id
     */
    public function setItemId($id)
    {
        $this->_itemId = (int) $id;
    }

    /**
     * Getter method for current Item Id.
     *
     * @return number
     */
    public function getItemId()
    {
        return $this->_itemId;
    }

    /**
     * Find the active window in the config based on a given Hash.
     *
     * @param string $activeWindowHash
     *
     * @return array|bool
     */
    public function findActiveWindow($activeWindowHash)
    {
        $activeWindows = $this->getConfig()->getPointer('aw');

        return isset($activeWindows[$activeWindowHash]) ? $activeWindows[$activeWindowHash] : false;
    }
}
