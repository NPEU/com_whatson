<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

#require_once JPATH_COMPONENT . '/helpers/route.php';

// Get an instance of the controller prefixed by WhatsOn
$controller = JControllerLegacy::getInstance('WhatsOn');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();