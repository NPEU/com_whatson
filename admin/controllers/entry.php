<?php
// ALMOST CERTAINLY DON'T NEED THIS - PROBABLY CAN DELETE
/**
 * @package     Joomla.Administrator
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

/**
 * WhatsOn Entry Controller
 */
class WhatsOnControllerEntry extends JControllerForm
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     \JControllerLegacy
     * @throws  \Exception
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->view_list = 'whatson';
    }
}
