<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Whatson\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;


/**
 * Whatson Component Controller
 */
class DisplayController extends BaseController {
    protected $default_view = 'whatson';

    public function display($cachable = false, $urlparams = [])
    {
        return parent::display($cachable, $urlparams);
    }
}