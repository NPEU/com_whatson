<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

// Load the Admin language file to avoid repeating form language strings:
$lang = JFactory::getLanguage();
$extension = 'com_whatson';
$base_dir = JPATH_COMPONENT_ADMINISTRATOR;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

/**
 * HTML View class for the WhatsOn Component
 */
class WhatsOnViewWhatsOn extends JViewLegacy
{
    // Overwriting JView display method
    function display($tpl = null)
    {
        $user = JFactory::getUser();


        // We may not actually want to show the form at this point (though we could if we wanted to
        // include the form AND the list on the same page - especially if it's displayed via a
        // modal), but it's useful to have the form so we can retrieve language strings without
        // having to manually redeclare them, along with any other properties of the form that may be
        // useful:
        //$this->setModel($this->getModel('whatson'));
        #jimport('joomla.application.component.model');
        #JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_whatson/models');
        require JPATH_SITE . '/components/com_whatson/models/entry.php';
        $whatson_model = JModelLegacy::getInstance('Entryform', 'WhatsOnModel');
        #echo '<pre>'; var_dump($whatson_model); echo '</pre>'; exit;
        $whatson_site_model = JModelLegacy::getInstance('WhatsOn', 'WhatsOnModel');
        #echo '<pre>'; var_dump($whatson_site_model); echo '</pre>'; exit;
        $form = $whatson_model->getForm();
        #echo '<pre>'; var_dump($form); echo '</pre>'; exit;


        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $menu   = $menus->getActive();

        $jinput = $app->input;
        $edit_user_row = $jinput->get('edit', false);
        $start_date = WhatsOnHelper::getStartDate();

        $current_week_timestamps = WhatsOnHelper::getWeekDayTimestamps();
        $current_view_uri    = explode('?', JUri::getInstance()->toString())[0];

        if ($start_date < $current_week_timestamps['Monday']) {
            // Date is in the past, check for permission:
            if (!$user->authorise('core.admin')) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_WHATSON_PAST_DATE_MESSAGE'), 'error');
                #$start_date = $current_week_timestamps['Monday'];
                $app->redirect(JRoute::_($current_view_uri));
            }
        }

        #echo '<pre>'; var_dump($start_date); echo '</pre>';exit;

        $staff = $whatson_site_model->getStaff();

        // Add fake users for 'Events' and 'Visitors':
        // Note it would probably be better to move these definitions to the Config.
        $visitors_user = array(
            'id'         => 2,
            'name'       => 'Visitors',
            'first_name' => '',
            'last_name'  => 'VISITORS',
            'tel'        => '',
            'room'       => ''
        );
        array_unshift($staff, $visitors_user);

        $events_user = array(
            'id'         => 1,
            'name'       => 'Events',
            'first_name' => '',
            'last_name'  => 'EVENTS',
            'tel'        => '',
            'room'       => ''
        );
        array_unshift($staff, $events_user);
        ////

        $this->staff = $staff;

        // Get the parameters
        #$this->com_params  = JComponentHelper::getParams('com_whatson');
        $this->menu_params = $menu->params;

        $layout = $this->getLayout();
        if ($layout != 'default') {
            $breadcrumb_title = $breadcrumb_title  = JText::_('COM_WHATSON_PAGE_TITLE_' . strtoupper($layout));

            #echo '<pre>'; var_dump($breadcrumb_title); echo '</pre>'; exit;

            $pathway = $app->getPathway();
            $pathway->addItem($breadcrumb_title);
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

            return false;
        }


/*
           //$authorised = $this->user->authorise('core.edit', 'com_whatson');
$is_own = false;
            if ($this->user->authorise('core.edit.own', 'com_whatson') && ($this->user->id == $staff_member['id'])) {
                $is_own = true;
            }

*/
        // Assign data to the view:
        $this->items = $this->get('Items');
        #echo '<pre>'; var_dump($this->items); echo '</pre>'; exit;
        #$this->items = $this->get('AllItems');
        #$this->items = $this->get('UnpublishedItems');

        $this->user  = $user;
        $this->user_profile = JUserHelper::getProfile($user->id);
        $this->title = $menu->title;
        $this->form  = $form;

        $this->editing_user_row = $edit_user_row;
        $this->can_edit_all = $this->user->authorise('core.edit', 'com_whatson');

        #echo '<pre>'; var_dump($this->can_edit_all); echo '</pre>';exit;

        $this->start_date = $start_date;
        $this->current_view_uri = $current_view_uri . '?date=' . $jinput->get('date', date('Y-m-d', $start_date));
        $this->week_timestamps = WhatsOnHelper::getWeekDayTimestamps($start_date);
        $this->current_week_timestamps = $current_week_timestamps;
        #$this->week_timestamps = WhatsOnHelper::getWeekDayTimestamps(1609632000);

        /*foreach ($this->week_timestamps as $stamp) {
            echo '<pre>'; var_dump(WhatsOnHelper::getDateReadable($stamp)); echo '</pre>';
        }
        exit;*/

        // Display the view
        parent::display($tpl);
    }
}
