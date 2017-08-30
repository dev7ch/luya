<?php

namespace luya\frontendgroup;

use luya\admin\models\Property;
use luya\base\CoreModuleInterface;
use luya\cms\Menu;
use luya\cms\models\Property as CmsProperty;
use luya\frontendgroup\properties\GroupAuthProperty;
use Yii;
use yii\base\BootstrapInterface;

/**
 * FrontendGroup Module.
 *
 * This Module must be bootstraped in your config in order to protect the menu items.
 *
 * @author Basil Suter <basil@nadar.io>
 */
final class Module extends \luya\base\Module implements BootstrapInterface, CoreModuleInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \yii\base\BootstrapInterface::bootstrap()
     */
    public function bootstrap($app)
    {
        $findProperty = Property::findOne(['class_name' => GroupAuthProperty::className()]);
        if ($findProperty) {
            Yii::$app->menu->on(Menu::MENU_ITEM_EVENT, [$this, 'hideElements'], CmsProperty::findAll(['admin_prop_id' => $findProperty->id]));
        }
    }

    /**
     * Hide the elements which are protected by the propertie.
     *
     * @param \luya\cms\Menu::MENU_ITEM_EVENT $event
     */
    public function hideElements($event)
    {
        $properties = $event->data;
        foreach ($properties as $prop) {
            if ($prop->object->requiresAuth() && $event->item->navId == $prop->nav_id) {
                $event->visible = false;
                foreach ($this->frontendUsers as $userComponent) {
                    $user = Yii::$app->get($userComponent);
                    if (!$user->isGuest && $user->inGroup($prop->object->getGroups())) {
                        $event->visible = true;
                    }
                }
            }
        }
    }

    /**
     * Define all available frontend groups.
     *
     * @var array An array contain all frontend groups which are available, like
     *
     * ```php
     * $frontendGroups = ['patient', 'expert', 'those', 'theothers'];
     * ```
     */
    public $frontendGroups = [];

    /**
     * @var array Define an array with the names of defined yii\web\User's from the config. Example defintion
     *            in the config. This array will be used to perform group permission checks based on the
     *            luya\web\GroupUserIdentityInterface implemantions of `authGroups()`.
     *
     * ```
     * 'patient' => [
     *     'class' => 'luya\web\GroupUser',
     *     'identityClass' => 'app\models\frontend\Patient',
     * ],
     * 'expert' => [
     *     'class' => 'luya\web\GroupUser',
     *     'identityClass' => 'app\models\frontend\Expert',
     * ],
     * ```
     *
     * would look like this in frontendUsers array
     *
     * ```
     * $frontendUsers = ['patient', 'expert'];
     * ```
     *
     * You can define this property via the application configuration.
     */
    public $frontendUsers = [];
}
