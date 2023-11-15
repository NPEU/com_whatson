<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
    'textPrefix' => 'COM_WHATSON',
    'formURL'    => 'index.php?option=com_whatson',
];

/*
$displayData = [
    'textPrefix' => 'COM_WHATSON',
    'formURL'    => 'index.php?option=com_whatson',
    'helpURL'    => '',
    'icon'       => 'icon-globe whatson',
];
*/

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_whatson') || count($user->getAuthorisedCategories('com_whatson', 'core.create')) > 0) {
    $displayData['createURL'] = 'index.php?option=com_whatson&task=entry.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);