<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Whatson\Site\View\Whatson;

defined('_JEXEC') or die;

#use Joomla\CMS\Helper\TagsHelper;
#use Joomla\CMS\Plugin\PluginHelper;
#use Joomla\Event\Event;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserHelper;

use NPEU\Component\Whatson\Administrator\Helper\WhatsonHelper;

/**
 * Whatson Component HTML View
 */
class HtmlView extends BaseHtmlView {

    /**
     * The page parameters
     *
     * @var    \Joomla\Registry\Registry|null
     */
    protected $params;

    /**
     * The item model state
     *
     * @var    \Joomla\Registry\Registry
     */
    protected $state;

    // This allows alternate views to overide this and supply a different title:
    protected function getTitle($title = '') {
        return $title;
    }

    public function display($template = null)
    {
        $app = Factory::getApplication();

        #$this->state  = $this->get('State');
        $this->params = $app->getParams();
        $this->items  = $this->get('Items');

        #echo 'items<pre>'; var_dump($this->items); echo '</pre>'; exit;

        $user = $app->getIdentity();
        $user_is_root = $user->authorise('core.admin');
        $this->user = $user;


        $whatson_model = $app->bootComponent('com_whatson')->getMVCFactory()->createModel('Form', 'Site');
        $whatson_site_model =  $app->bootComponent('com_whatson')->getMVCFactory()->createModel('WhatsOn', 'Site');

        // We may not actually want to show the form at this point (though we could if we wanted to
        // include the form AND the record on the same page - especially if it's displayed via a
        // modal), but it's useful to have the form so we can retrieve language strings without
        // having to manually reclare them, along with any other properties of the form that may be
        // useful:
        $this->form = $whatson_model->getForm('Form');
        #echo 'items<pre>'; var_dump($this->form); echo '</pre>'; exit;

        // Load admin lang file for use in the form:
        $app->getLanguage()->load('com_whatson', JPATH_COMPONENT_ADMINISTRATOR);

        $uri    = Uri::getInstance();
        $menus  = $app->getMenu();
        $menu   = $menus->getActive();

        $this->title = $this->getTitle($menu->title);
        #echo '<pre>'; var_dump($this->title); echo '</pre>'; exit;
        $this->menu_params = $menu->getParams();

        $input = $app->input;
        $edit_user_row = $input->get('edit', false);
        $start_date = WhatsOnHelper::getStartDate();
        #echo 'start_date<pre>'; var_dump($start_date); echo '</pre>'; exit;

        $current_week_timestamps = WhatsOnHelper::getWeekDayTimestamps();
        $current_view_uri    = explode('?', Uri::getInstance()->toString())[0];

        if ($start_date < $current_week_timestamps['Monday']) {
            // Date is in the past, check for permission:
            if (!$user->authorise('core.admin')) {
                Factory::getApplication()->enqueueMessage(Text::_('COM_WHATSON_PAST_DATE_MESSAGE'), 'error');
                #$start_date = $current_week_timestamps['Monday'];
                $app->redirect(Route::_($current_view_uri));
            }
        }

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

        #echo 'this->staff<pre>'; var_dump($this->staff); echo '</pre>'; exit;

        // Add to breadcrumbs:
        $pathway = $app->getPathway();

        $layout = $this->getLayout();
        if ($layout != 'default') {

            $page_title = Text::_('COM_WHATSON_PAGE_TITLE_' . strtoupper($layout));
            $pathway->addItem($page_title);
            $menu->title = $page_title;
        }

        // Check for errors.
        $errors = $this->get('Errors', false);

        if (!empty($errors)) {
            Log::add(implode('<br />', $errors), Log::WARNING, 'jerror');

            return false;
        }


        // Assign additional data to the view:
        $this->user_profile = UserHelper::getProfile($user->id);
        $this->title = $menu->title;

        $this->editing_user_row = $edit_user_row;
        $this->can_edit_all = $this->user->authorise('core.edit', 'com_whatson');



        $this->start_date = $start_date;
        $this->current_view_uri = $current_view_uri . '?date=' . $input->get('date', date('Y-m-d', $start_date));
        $this->week_timestamps = WhatsOnHelper::getWeekDayTimestamps($start_date);
        $this->current_week_timestamps = $current_week_timestamps;

        #echo '<pre>'; var_dump($this->current_week_timestamps); echo '</pre>'; exit;
        // Call the parent display to display the layout file
        parent::display($template);

        /*

        $user = JFactory::getUser();
        $user_is_root = $user->authorise('core.admin');

        $item = $this->get('Item');
        // We may not actually want to show the form at this point (though we could if we wanted to
        // include the form AND the record on the same page - especially if it's displayed via a
        // modal), but it's useful to have the form so we can retrieve language strings without
        // having to manually reclare them, along with any other properties of the form that may be
        // useful:
        $form = $this->get('Form');
        #echo '<pre>'; var_dump($item); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($form); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($this->getLayout()); echo '</pre>'; exit;

        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $menu   = $menus->getActive();
        #echo '<pre>'; var_dump($menu); echo '</pre>'; exit;
        #echo '<pre>'; var_dump(JRoute::_($menu->link)); echo '</pre>'; exit;
        #echo '<pre>'; var_dump(URI::base()); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($item->id); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($user, $item); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($user->id, $item->created_by); echo '</pre>'; exit;

        $this->return_page = base64_encode(URI::base() . $menu->route);


        $is_new = empty($item->id);
        $is_own = false;
        if (!$is_new && ($user->id == $item->created_by)) {
            $is_own = true;
        }


        if ($user_is_root) {
            $authorised = true;
        } elseif ($is_new) {
            $authorised = $user->authorise('core.create', 'com_whatson');
        } elseif ($is_own) {
            $authorised = $user->authorise('core.edit.own', 'com_whatson');
        }
        else {
            $authorised = $user->authorise('core.edit', 'com_whatson');
        }

        if ($authorised !== true && $this->getLayout() == 'form') {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

            return false;
        }


        // Assign data to the view
        $this->item = $item;
        // Although we're not actually showing the form, it's useful to use it to be able to show
        // the field names without having to explicitly state them (more DRY):
        $this->form = $form;

        */




        /*
        $app = Factory::getApplication();

        $this->item   = $this->get('Item');
        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');

        // Create a shortcut for $item.
        $item = $this->item;

        $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

        $temp         = $item->params;
        $item->params = clone $app->getParams();
        $item->params->merge($temp);

        $offset = $this->state->get('list.offset');

        $app->triggerEvent('onContentPrepare', array('com_weblinks.weblink', &$item, &$item->params, $offset));

        $item->event = new \stdClass;

        $results = $app->triggerEvent('onContentAfterTitle', array('com_weblinks.weblink', &$item, &$item->params, $offset));
        $item->event->afterDisplayTitle = trim(implode("\n", $results));

        $results = $app->triggerEvent('onContentBeforeDisplay', array('com_weblinks.weblink', &$item, &$item->params, $offset));
        $item->event->beforeDisplayContent = trim(implode("\n", $results));

        $results = $app->triggerEvent('onContentAfterDisplay', array('com_weblinks.weblink', &$item, &$item->params, $offset));
        $item->event->afterDisplayContent = trim(implode("\n", $results));

        parent::display($tpl);
        */







        /*// Assign data to the view
        $this->msg = 'Get from API';
        */

    }

}