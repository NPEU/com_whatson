<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

use Joomla\String\StringHelper;

/**
 * WhatsOn Entry Model
 */
class WhatsOnModelEntry extends JModelAdmin
{
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type    The table name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     */
    public function getTable($type = 'WhatsOn', $prefix = 'WhatsOnTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed    A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_whatson.entry',
            'entry',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form))
        {
            return false;
        }

        // Determine correct permissions to check.
        /*if ($this->getState('entry.id'))
        {
            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');
        }
        else
        {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }*/

        // Modify the form based on access controls.
        /*if (!$this->canEditState((object) $data))
        {
            // Disable fields for display.
            $form->setFieldAttribute('state', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('state', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
        }*/

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        // We won't need to get the data directly - the WhatsOn doesn't work like that.
        return array();
        /*
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState(
            'com_whatson.edit.entry.data',
            array()
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
        */
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     */
    /*public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk))
        {
            // Convert the metadata field to an array.
            $registry = new Registry;
            $registry->loadString($item->metadata);
            $item->metadata = $registry->toArray();

            // Convert the images field to an array.
            $registry = new Registry;
            $registry->loadString($item->images);
            $item->images = $registry->toArray();

            if (!empty($item->id))
            {
                $item->tags = new JHelperTags;
                $item->tags->getTagIds($item->id, 'com_weblinks.weblink');
                $item->metadata['tags'] = $item->tags;
            }
        }

        return $item;
    }*/

    /**
     * Prepare and sanitise the table data prior to saving.
     *
     * @param   JTable  $table  A reference to a JTable object.
     *
     * @return  void
     */
    /*protected function prepareTable($table)
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();

        $table->text = htmlspecialchars_decode($table->text, ENT_QUOTES);

        /*
        $table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
        $table->alias = JApplicationHelper::stringURLSafe($table->alias);

        if (empty($table->alias))
        {
            $table->alias = JApplicationHelper::stringURLSafe($table->title);
        }

        $table->modified    = $date->toSql();
        $table->modified_by = $user->id;

        if (empty($table->id))
        {
            $table->created    = $date->toSql();
            $table->created_by = $user->id;
        }
        * /
    }*/

    /**
     * Method to prepare the saved data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     */
    /*public function savefilter($data)
    {
        $app    = JFactory::getApplication();
        $input  = $app->input;

        echo '<pre>'; var_dump($input); echo '</pre>'; exit;
    }*/

    /**
     * Method to prepare the saved data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     */
    public function save($data)
    {
        #$is_new = empty($data['id']);
        $app    = JFactory::getApplication();
        $input  = $app->input;
        $db     = JFactory::getDbo();
        $user   = JFactory::getUser();
        #echo '<pre>'; var_dump($input); echo '</pre>'; exit;

        // This form can also be used to save a filter, instead of updating the WhatsOn, so check
        // for that here.
        $action           = $input->get('action', false);
        #echo '<pre>'; var_dump($action); echo '</pre>'; exit;

        $new_filter_name  = $input->get('new_filter_name', false);
        $filter_value     = $input->getString('whatson_filter', '');


        $profile_key = 'staffprofile.whatson_filters';
        #echo '<pre>'; var_dump($new_filter_name); echo '</pre>'; #exit;
        #echo '<pre>'; var_dump($filter_value); echo '</pre>'; exit;

        if ($action == 'add-new-filter' || $action == 'delete-filter') {

            // We're CRUDing a filter, so we need to get any existing ones, prepare the data,
            // then process.
            // Template: {"whatson_filters<<<N>>>":{"whatson_filter_name":"<<<NAME>>>","whatson_filter_value":"<<<NAME>>>"}}
            $query = $db->getQuery(true);

            $query->select($db->qn('profile_value'))
                  ->from($db->qn('#__user_profiles'))
                  ->where($db->qn('user_id') .' = ' . $db->q($user->id))
                  ->where($db->qn('profile_key') .' = ' . $db->q($profile_key));
            $db->setQuery($query);

            $whatson_filters = $db->loadResult();
            #echo '<pre>'; var_dump($whatson_filters); echo '</pre>';

            $filter_names = [];
            if (empty($whatson_filters)) {
                $whatson_filters = [];
                $has_filters = false;
            } else {
                $has_filters = true;

                // Make a list of names to make it easier to check if one exists:
                if (!empty($whatson_filters)) {
                    $whatson_filters = json_decode($whatson_filters, true);

                    foreach ($whatson_filters as $key => $filter) {
                        $filter_names[] = $filter['whatson_filter_name'];
                    }
                }
                ////
            }

            $whatson_filters_json = false;

            // If we want to delete a filter ...
            if ($action == 'delete-filter') {
                // ... and the ARE some in the database:
                if (!$has_filters) {
                    // No filters to delete - quit.
                    return true;
                }

                // We need to rebuild the filters array, skipping the one we're deleting, so the
                // query below would be an UPDATE, but if we may end up with an empty array
                // (last filter deleted) we need to delete the row.
                $tmp_filters = [];
                $i = 0;
                foreach ($whatson_filters as $key => $filter) {

                    if ($filter['whatson_filter_value'] == $filter_value) {
                        continue;
                    }
                    $tmp_filters['whatson_filters' . $i] = $filter;
                    $i++;
                }
                $whatson_filters = $tmp_filters;

                if (!empty($whatson_filters)) {
                    $whatson_filters_json = json_encode($whatson_filters);
                }
                ////
                #echo '<pre>'; var_dump($filter_value); echo '</pre>'; #exit;
                #echo '<pre>'; var_dump($whatson_filters); echo '</pre>'; exit;
            }

            // If we want to add a filter and we have a name and value:
            if ($action == 'add-new-filter' && !empty($new_filter_name) && !empty($filter_value)) {

                #echo '<pre>'; var_dump('DB: ' . $whatson_filters); echo '</pre>'; exit;


                // If the name exists we have to override it:
                if (in_array($new_filter_name, $filter_names)) {
                    $filter_names[$new_filter_name] = $filter_value;
                } else {
                    // Create a new filter entry:
                    $new_filter = [
                        'whatson_filter_name'  => $new_filter_name,
                        'whatson_filter_value' => $filter_value
                    ];
                    $whatson_filters['whatson_filters' . count($whatson_filters)] = $new_filter;
                }

                $whatson_filters_json = json_encode($whatson_filters);

                #echo '<pre>'; var_dump((string) $query); echo '</pre>'; exit;
                #echo '<pre>'; var_dump($whatson_filters); echo '</pre>'; exit;
                #echo '<pre>'; var_dump($filter_names); echo '</pre>'; exit;
                #echo '<pre>'; var_dump($whatson_filters_json); echo '</pre>'; exit;
                #echo '<pre>'; var_dump($has_filters); echo '</pre>'; exit;
                #$query = $db->getQuery(true);

            }

            $query->clear();

            #echo '<pre>'; var_dump($whatson_filters_json); echo '</pre>'; #exit;
            #echo '<pre>'; var_dump($has_filters); echo '</pre>'; exit;

            // If we have some existing filters AND one has been submitted/deleted, UPDATE:
            if ($has_filters && !empty($whatson_filters_json)) {
                $query->update($db->qn('#__user_profiles'))
                      ->set($db->qn('profile_value') . ' = ' . $db->q($whatson_filters_json))
                      ->where(array(
                            $db->qn('user_id') . ' = ' . $user->id,
                            $db->qn('profile_key') . ' = ' . $db->q($profile_key)
                        ));

                $db->setQuery($query);
                $result = $db->execute();

                return true;
            }

            // If we have some existing filters, but the last one has been deleted, DELETE:
            if ($has_filters && empty($whatson_filters_json)) {
                $query->delete($db->qn('#__user_profiles'))
                      ->where(array(
                            $db->qn('user_id') . ' = ' . $user->id,
                            $db->qn('profile_key') . ' = ' . $db->q($profile_key)
                        ));

                $db->setQuery($query);
                $result = $db->execute();

                return true;
            }

            // We don't have any existing filters but one has been submitted, so INSERT that:
            if (!$has_filters && !empty($whatson_filters_json)) {
                // Prepare the insert query.
                $columns = array('user_id', 'profile_key', 'profile_value', 'ordering');
                $values  = array($user->id, $db->q($profile_key), $db->q($whatson_filters_json), 32);

                $query->insert($db->qn('#__user_profiles'))
                      ->columns($db->qn($columns))
                      ->values(implode(',', $values));

                // Set the query using our newly populated query object and execute it.
                $db->setQuery($query);
                $db->execute();

                return true;
            }

        }


        if (array_key_exists('start_date', $data) && array_key_exists('user_id', $data)) {
            $entry_id = $data['start_date'] . '.' . $data['user_id'];
            // WhatsOn entries aren't like traditional 'items' so there's a bit of a mismatch between
            // this and Joomla's default handling of things. Best effort so far is to simply check here
            // if there's already a row, and if there is, add the `id` to the data so the entry get
            // updated, otherwise added.


            $query = $db->getQuery(true);

            $query->select($query->qn('id'))
                ->from($query->qn('#__whatson'))
                ->where($query->qn('id') .' = ' . $query->q($entry_id));
            $db->setQuery($query);

            #echo '<pre>'; var_dump((string) $query); echo '</pre>'; exit;

            $entry_exists = $db->loadResult();

            if ($entry_exists) {
                $data['id'] = $entry_id;
            }
        }

        #echo '<pre>'; var_dump($entry_exists); echo '</pre>'; exit;

        // Get parameters:
        //$params = JComponentHelper::getParams(JRequest::getVar('option'));

        // For reference if needed:
        // By default we're only looking for and acting upon the 'email admins' setting.
        // If any other settings are related to this save method, add them here.
        /*$email_admins_string = $params->get('email_admins');
        if (!empty($email_admins_string) && $is_new) {
            $email_admins = explode(PHP_EOL, trim($email_admins_string));
            foreach ($email_admins as $email) {
                // Sending email as an array to make it easier to expand; it's quite likely that a
                // real app would need more info here.
                $email_data = array('email' => $email);
                $this->_sendEmail($email_data);
            }
        }*/

        // Alter the title for save as copy
        /*if ($app->input->get('task') == 'save2copy')
        {
            list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
            $data['title']    = $title;
            $data['alias']    = $alias;
            $data['state']    = 0;
        }*/

        /*if ($input->get('task') == 'save2copy') {
            $origTable = clone $this->getTable();
            $origTable->load($input->getInt('id'));

            // Note is using custom category field title, you need to change 'catid':
            if ($data['title'] == $origTable->title) {
                list($title, $alias) = $this->generateNewBrandTitle($data['catid'], $data['alias'], $data['title']);
                $data['title'] = $title;
                $data['alias'] = $alias;
            } else {
                if ($data['alias'] == $origTable->alias) {
                    $data['alias'] = '';
                }
            }

            $data['state'] = 0;
        }*/

        // Automatic handling of alias for empty fields
        // Taken from com_content/models/article.php
        /*if (in_array($input->get('task'), array('apply', 'save', 'save2new'))) {
            if (empty($data['alias'])) {
                if (JFactory::getConfig()->get('unicodeslugs') == 1) {
                    $data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
                } else {
                    $data['alias'] = JFilterOutput::stringURLSafe($data['title']);
                }

                $table = JTable::getInstance('WhatsOn', 'WhatsOnTable');

                if ($table->load(array('alias' => $data['alias']))) {
                    $msg = JText::_('COM_CONTENT_SAVE_WARNING');
                }

                #list($title, $alias) = $this->generateNewWhatsOnTitle($data['alias'], $data['title']);
                list($title, $alias) = $this->generateNewTitle($data['alias'], $data['title']);
                $data['alias'] = $alias;

                if (isset($msg)) {
                    JFactory::getApplication()->enqueueMessage($msg, 'warning');
                }
            }
        }*/

        return parent::save($data);
    }

    /**
     * Method to change the title & alias.
     *
     * @param   integer  $category_id  The id of the parent.
     * @param   string   $alias        The alias.
     * @param   string   $name         The title.
     *
     * @return  array  Contains the modified title and alias.
     */
    /*protected function generateNewTitle($category_id, $alias, $name)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias, 'catid' => $category_id)))
        {
            if ($name == $table->title)
            {
                $name = JString::increment($name);
            }

            $alias = JString::increment($alias, 'dash');
        }

        return array($name, $alias);
    }*/

    /**
     * Copied from libraries/src/MVC/Model/AdminModel.php because it uses a hard-coded field name:
     * catid.
     *
     * Method to change the title & alias.
     *
     * @param   string   $alias        The alias.
     * @param   string   $title        The title.
     *
     * @return  array  Contains the modified title and alias.
     */
    /*protected function generateNewWhatsOnTitle($alias, $title)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias)))
        {
            $title = StringHelper::increment($title);
            $alias = StringHelper::increment($alias, 'dash');
        }

        return array($title, $alias);
    }*/


    /**
     * Method to get the script that have to be included on the form
     *
     * @return string   Script files
     */
    /*public function getScript()
    {
        #return 'administrator/components/com_helloworld/models/forms/helloworld.js';
        return '';
    }*/

    /**
     * Delete this if not needed. Here for reference.
     * Method to get the data that should be injected in the form.
     *
     * @return  bool  Email success/failed to send.
     */
    /*private function _sendEmail($email_data)
    {
            $app        = JFactory::getApplication();
            $mailfrom   = $app->getCfg('mailfrom');
            $fromname   = $app->getCfg('fromname');
            $sitename   = $app->getCfg('sitename');
            $email      = JStringPunycode::emailToPunycode($email_data['email']);

            // Ref: JText::sprintf('LANG_STR', $var, ...);

            $mail = JFactory::getMailer();
            $mail->addRecipient($email);
            $mail->addReplyTo($mailfrom);
            $mail->setSender(array($mailfrom, $fromname));
            $mail->setSubject(JText::_('COM_ALERTS_EMAIL_ADMINS_SUBJECT'));
            $mail->setBody(JText::_('COM_ALERTS_EMAIL_ADMINS_BODY'));
            $sent = $mail->Send();

            return $sent;
    }*/
}
