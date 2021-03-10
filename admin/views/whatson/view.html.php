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
 * WhatsOn WhatsOn View
 */
class WhatsOnViewWhatsOn extends JViewLegacy
{
    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the WhatsOn view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    function display($tpl = null)
    {
        /*
        $this->state         = $this->get('State');
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');


        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
        */
        $this->addToolbar();
        #$this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     */
    protected function addToolBar()
    {

        //$canDo = WhatsOnHelper::getActions();
        $canDo = JHelperContent::getActions('com_whatson');
        $user  = JFactory::getUser();

        $title = JText::_('COM_WHATSON_MANAGER_RECORDS');
        /*
        if ($this->pagination->total) {
            $title .= "<span style='font-size: 0.5em; vertical-align: middle;'> (" . $this->pagination->total . ")</span>";
        }
        */
        // Note 'question-circle' is an icon/classname. Change to suit in all views.
        JToolBarHelper::title($title, 'calendar');
        /*
        JToolBarHelper::addNew('entry.add');
        if (!empty($this->items)) {
            JToolBarHelper::editList('entry.edit');
            JToolBarHelper::deleteList('', 'whatson.delete');
        }
        */
        /*
        if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_whatson', 'core.create')) > 0) {
            JToolbarHelper::addNew('entry.add');
        }

        if ($canDo->get('core.edit') || $canDo->get('core.edit.own'))
        {
            JToolbarHelper::editList('entry.edit');
        }

        if ($canDo->get('core.edit.state'))
        {
            JToolbarHelper::publish('whatson.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('whatson.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            //JToolbarHelper::custom('entry.featured', 'featured.png', 'featured_f2.png', 'JFEATURE', true);
            //JToolbarHelper::custom('entry.unfeatured', 'unfeatured.png', 'featured_f2.png', 'JUNFEATURE', true);
            //JToolbarHelper::archiveList('entry.archive');
            //JToolbarHelper::checkin('entry.checkin');
        }


        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
        {
            JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'whatson.delete', 'JTOOLBAR_EMPTY_TRASH');
        }
        elseif ($canDo->get('core.edit.state'))
        {
            JToolbarHelper::trash('whatson.trash');
        }
        */
        if ($user->authorise('core.admin', 'com_whatson') || $user->authorise('core.options', 'com_whatson'))
        {
            JToolbarHelper::preferences('com_whatson');
        }

    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_WHATSON_ADMINISTRATION'));
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    /*protected function getSortFields()
    {
        return array(
            'a.title' => JText::_('COM_WHATSON_RECORDS_NAME'),
            'a.owner_user_id' => JText::_('COM_WHATSON_RECORDS_OWNER'),
            'a.state' => JText::_('COM_WHATSON_PUBLISHED'),
            'a.id'    => JText::_('COM_WHATSON_ID')
        );
    }*/
}
