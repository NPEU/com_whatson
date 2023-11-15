<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Whatson\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;


/**
 * Whatson Component Controller
 */
class DisplayController extends BaseController {

    public function display($cachable = false, $urlparams = []) {
        $viewName = $this->input->get('view', '');
        $cachable = true;
        if ($viewName == 'form' || Factory::getApplication()->getIdentity()->get('id')) {
            $cachable = false;
        }

        $safeurlparams = [
            'id'   => 'INT', /* should be ARRAY if using `id:alias` style ids */
            'view' => 'CMD',
            'lang' => 'CMD',
        ];

        parent::display($cachable, $safeurlparams);

        return $this;
    }

}