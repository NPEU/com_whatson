<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Whatson\Administrator\Model;

defined('_JEXEC') or die;


#use Joomla\CMS\Form\Form;
#use Joomla\CMS\Helper\TagsHelper;
#use Joomla\CMS\Language\Associations;
#use Joomla\CMS\Language\LanguageHelper;
#use Joomla\CMS\UCM\UCMType;
#use Joomla\CMS\Versioning\VersionableModelTrait;
#use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;
#use Joomla\Registry\Registry;
#use Joomla\String\StringHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;


/**
 * Entry Model
 */
class EntryModel extends AdminModel
{
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type    The table name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  \Joomla\CMS\Table\Table  A \Joomla\CMS\Table\Table object
     */
    /*public function getTable($type = 'Whatson', $prefix = 'WhatsonTable', $config = array())
    {
        return \Joomla\CMS\Table\Table::getInstance($type, $prefix, $config);
    }*/

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

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        return [];
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
        if ($item = parent::getItem($pk)) {
            // Convert the metadata field to an array.
            $registry = new Registry;
            $registry->loadString($item->metadata);
            $item->metadata = $registry->toArray();

            // Convert the images field to an array.
            $registry = new Registry;
            $registry->loadString($item->images);
            $item->images = $registry->toArray();

            if (!empty($item->id)) {
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
     * @param   \Joomla\CMS\Table\Table  $table  A reference to a \Joomla\CMS\Table\Table object.
     *
     * @return  void
     */
    /*protected function prepareTable($table)
    {
        $date = Factory::getDate();
        $user = Factory::getApplication()->getIdentity();

        $table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);


        if (empty($table->alias)) {
            $table->alias = ApplicationHelper::stringURLSafe($table->title);
        }

        $table->modified    = $date->toSql();
        $table->modified_by = $user->id;

        if (empty($table->id)) {
            $table->created    = $date->toSql();
            $table->created_by = $user->id;
        }

        /*if (empty($table->id)) {
            // Set the values

            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db    = $this->getDbo();
                $query = $db->getQuery(true)
                    ->select('MAX(ordering)')
                    ->from($db->quoteName('#__weblinks'));

                $db->setQuery($query);
                $max = $db->loadResult();

                $table->ordering = $max + 1;
            } else {
                // Set the values
                $table->modified    = $date->toSql();
                $table->modified_by = $user->id;
            }
        }

        // Increment the weblink version number.
        $table->version++;*  /
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
        $is_new = empty($data['id']);
        $app    = Factory::getApplication();
        $input  = $app->input;
        $user   = $app->getIdentity();
        $db     = $this->getDbo();

        // Get parameters:
        $params = ComponentHelper::getParams($input->get('option'));

        // This form can also be used to save a filter, instead of updating the WhatsOn, so check
        // for that here.
        $action           = $input->get('action', false);

        $new_filter_name  = $input->get('new_filter_name', false);
        $filter_value     = $input->getString('whatson_filter', '');


        $profile_key = 'staffprofile.whatson_filters';

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
            }

            // If we want to add a filter and we have a name and value:
            if ($action == 'add-new-filter' && !empty($new_filter_name) && !empty($filter_value)) {

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

        return parent::save($data);
    }
}
