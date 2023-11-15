<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Whatson\Administrator\Table;

defined('_JEXEC') or die;

#use Joomla\CMS\Tag\TaggableTableInterface;
#use Joomla\CMS\Tag\TaggableTableTrait;
#use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Table\Nested;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;


/**
 * Entry Table class.
 *
 * @since  1.0
 */
#class WhatsonTable extends Nested implements VersionableTableInterface, TaggableTableInterface
class WhatsonTable extends Table
{
    public function __construct(DatabaseDriver $db) {

        parent::__construct('#__whatson', 'id', $db);
    }
}
