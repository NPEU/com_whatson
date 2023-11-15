<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Whatson\Site\Model;

defined('_JEXEC') or die;

#use NPEU\Component\Whatson\Site\Helper\EntryHelper;
#use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

/**
 * Alt Component Model
 */
class AltModel extends \NPEU\Component\Whatson\Administrator\Model\WhatsonModel {

    public function getTable($name = '', $prefix = '', $options = [])
    {
        return 'whatson';
    }

}