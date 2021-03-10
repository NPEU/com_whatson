<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

/**
 * WhatsOn List Model
 */
class WhatsOnModelWhatsOn extends JModelList
{
    #protected $published = 1;

	/**
	 * Gets an array of objects from the results of database query.
	 *
	 * @param   string   $query       The query.
	 * @param   integer  $limitstart  Offset.
	 * @param   integer  $limit       The number of records.
	 *
	 * @return  object[]  An array of results.
	 *
	 * @since   3.0
	 * @throws  \RuntimeException
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$this->getDbo()->setQuery($query, $limitstart, $limit);

		return $this->getDbo()->loadObjectList('user_id');
	}


    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        $start_date = WhatsOnHelper::getStartDate();
        $db         = JFactory::getDbo();
        $query      = $db->getQuery(true);

        // Create the select statement.
        $query->select('*')
              ->from($db->qn('#__whatson'))
              ->where($db->qn('start_date') . ' = ' . $db->q($start_date));

        #echo '<pre>'; var_dump((string) $query); echo '</pre>';exit;

        return $query;
    }

    /**
     * Get all current active Staff Members.
     *
     * @return      string  An SQL query
     */
    public function getStaff()
    {
        // Initialize variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Create the select statement.
        $q  = 'SELECT u.id, u.name, up1.profile_value AS first_name, up2.profile_value AS last_name, up3.profile_value AS tel, up4.profile_value AS room ';
        $q .= 'FROM `#__users` u ';
        $q .= 'JOIN `#__user_usergroup_map` ugm ON u.id = ugm.user_id ';
        $q .= 'JOIN `#__usergroups` ug ON ugm.group_id = ug.id ';
        $q .= 'JOIN `#__user_profiles` up1 ON u.id = up1.user_id AND up1.profile_key = "firstlastnames.firstname" ';
        $q .= 'JOIN `#__user_profiles` up2 ON u.id = up2.user_id AND up2.profile_key = "firstlastnames.lastname" ';
        $q .= 'JOIN `#__user_profiles` up3 ON u.id = up3.user_id AND up3.profile_key = "staffprofile.tel" ';
        $q .= 'JOIN `#__user_profiles` up4 ON u.id = up4.user_id AND up4.profile_key = "staffprofile.room" ';
        $q .= 'WHERE ug.title = "Staff" ';
        $q .= 'AND u.block = 0 ';
        $q .= 'ORDER BY last_name, first_name;';

        $db->setQuery($q);
        if (!$db->execute($q)) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $staff_members = $db->loadAssocList();

        return $staff_members;
    }

    /**
     * Method to get an array of data items (published and unpublished).
     *
     * @return  mixed  An array of data items on success, false on failure.
     */
    /*public function getAllItems()
    {
        $this->published = '';
        return parent::getItems();
    }*/

    /**
     * Method to get an array of data items (unpublished only).
     *
     * @return  mixed  An array of data items on success, false on failure.
     */
    /*public function getUnpublishedItems()
    {
        $this->published = 0;
        return parent::getItems();
    }*/
}
