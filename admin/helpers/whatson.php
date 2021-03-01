<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

/**
 * WhatsOnHelper component helper.
 */
class WhatsOnHelper extends JHelperContent
{
    /**
     * Configure the Submenu. Delete if component has only one view.
     *
     * @param   string  The name of the active view.
     */
    /*public static function addSubmenu($vName = 'whatson')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_WHATSON_MANAGER_SUBMENU_RECORDS'),
            'index.php?option=com_whatson&view=whatson',
            $vName == 'whatson'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_WHATSON_MANAGER_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&view=categories&extension=com_whatson',
            $vName == 'categories'
        );
    }*/

    /**
     * Get the actions
     */
     /*
    public static function getActions($itemId = 0, $model = null)
    {
        jimport('joomla.access.access');
        $user   = JFactory::getUser();
        $result = new JObject;

        if (empty($itemId)) {
            $assetName = 'comwhatson';
        }
        else {
            $assetName = 'com_whatson.entry.'.(int) $itemId;
        }

        $actions = JAccess::getActions('com_whatson', 'component');

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        // Check if user belongs to assigned category and permit edit if so:
        if ($model) {
            $item  = $model->getItem($itemId);

            if (!!($user->authorise('core.edit', 'com_whatson')
            || $user->authorise('core.edit', 'com_content.category.' . $item->catid))) {
                $result->set('core.edit', true);
            }
        }

        return $result;
    }*/

}
