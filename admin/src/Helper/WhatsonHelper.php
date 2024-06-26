<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Whatson\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\User\UserHelper;

/**
 * DesignrequestssHelper Component Model
 */
#class DesignrequestsHelper extends ContentHelper
class WhatsonHelper
{
    public static function getArrayValue($array = array(), $key = false)
    {
        if (is_null($key) || $key === false) {
            return false;
        }

        if (!is_array($array)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        return false;
    }

    public static function getWhatsOnEntry($items, $staff_id, $day)
    {
        $entry = self::getArrayValue($items, $staff_id);
        #echo '<pre>'; var_dump($entry); echo '</pre>'; exit;
        // Send the value back if it's available:
        if ($entry && !empty($entry->$day)) {
            return $entry->$day;
        }

        return false;
    }

    public static function getWhatsOnPref($staff_id, $day, $week)
    {
        // Fake users (Events, Visitors) won't have an profile, and we just want to show a dash if
        // there's no value, so do that here:
        if ($staff_id < 602) {
            return '-';
        }

        // No entry found, check for user defaults in the profile:
        jimport('joomla.user.helper');
        $user = Factory::getUser();
        $profile = UserHelper::getProfile($staff_id);

        #echo '<pre>'; var_dump($profile->profile); echo '</pre>'; #exit;

        $whatson_pref_key_prefix = 'whatson-pref-day';

        $whatson_prefs = array();

        foreach ($profile->profile as $key => $value) {
            #echo '<pre>'; var_dump($value); echo '</pre>'; #exit;
            if (strpos($key, $whatson_pref_key_prefix) === 0) {
                $whatson_prefs[] = $value;
            }
        }

        // Check if there's ANY WhatsOn Prefs: (if user hasn't saved since Staff Profile plugin
        // was updated, there won't be)
        if (!empty($whatson_prefs)) {

            // Split the flat list of prefs into weeks:
            $prefs_by_week = array_chunk($whatson_prefs, 5);

            // This next bit is temporary. Currently there are 10 day fields for the prefs and I can't
            // tell if the user wants a 'blank' fortnight or if the fortnight isn't used. I'm assuming
            // the firner if all the fields are empty.
            // Ideally, when I get a chance, I'll change the profile to use a repeatable subform so that
            // there are only ever enough fields for the weeks the user want's to specify. This would
            // then also allow for a 3 or 4 week pattern:
            $fortnight_is_empty = true;
            if (array_key_exists(1, $prefs_by_week)) {
                foreach ($prefs_by_week[1] as $key => $value) {
                    if (!empty($value)) {
                        $fortnight_is_empty = false;
                        break;
                    }
                }
            }

            if ($fortnight_is_empty) {
                unset($prefs_by_week[1]);
            }
            ////

            #echo '<pre>'; var_dump($prefs_by_week); echo '</pre>'; #exit;

            // Most likely there will only be one week so start with that:
            $prefs_week = $prefs_by_week[0];

            // But if there's more than one, we need to select the correct one based on the given week:
            $n_pref_weeks = count($prefs_by_week);
            #echo '<pre>'; var_dump($n_pref_weeks); echo '</pre>'; #exit;
            #echo '<pre>Week '; var_dump($week); echo '</pre>'; #exit;
            #echo '<pre>Mod '; var_dump($week % $n_pref_weeks); echo '</pre>'; #exit;
            if ($n_pref_weeks > 1) {
                // If week of year % number of pref weeks is 0 it means we want the last pref week:
                $key = ($week % $n_pref_weeks == 0) ? $n_pref_weeks : $week % $n_pref_weeks;
                #echo '<pre>'; var_dump($key); echo '</pre>';
                $prefs_week = $prefs_by_week[$key - 1];
            }


            // We need to resolve Prefs stored as 'day1', 'day2' etc with 'mon', 'tue' etc.
            $days    = array('mon' => 0, 'tue' => 1, 'wed' => 2, 'thu' => 3, 'fri' => 4);
            $day_key = $days[$day];

            #echo '<pre>day_key: '; var_dump($day_key); echo '</pre>'; #exit;
            #echo '<pre>'; var_dump($prefs_week); echo '</pre>'; #exit;
            #echo '<pre>'; var_dump(array_key_exists($day_key, $prefs_week)); echo '</pre>'; exit;
            #echo '<pre>'; var_dump($prefs_week[$day_key]); echo '</pre>'; exit;
            $value = self::getArrayValue($prefs_week, $day_key);

            if (!empty($value)) {
                return $value;
            }
        }

        return false;
    }

    public static function getWhatsOnDefault()
    {
        // Otherwise, use the default value:
        $params = clone ComponentHelper::getParams('com_whatson');
        if (!empty($params->get('default_value'))) {
            return $params->get('default_value');
        }

        // A fallback in case the the config isn't set:
        return 'Unit';
    }

    public static function getWhatsOnValue($items, $staff_id, $day, $week)
    {
        $entry = self::getWhatsOnEntry($items, $staff_id, $day);
        if ($entry) {
            return $entry;
        }

        $pref = self::getWhatsOnPref($staff_id, $day, $week);
        if ($pref) {
            return $pref;
        }

        $default = self::getWhatsOnDefault();
        if ($default) {
            return $default;
        }

        return false;
    }

    public static function getWeekDayTimestamps($stamp = false, $longname = true)
    {
        if (!$stamp) {
            $stamp = time();
        }

        $return = array();

        $date_parts = getdate($stamp);
        $offset = $date_parts['weekday'] == 'Sunday'
                ? 6
                : $date_parts['wday'] - 1;

        #$start_week_stamp = mktime(0, 0, 0, $date_parts['mon'], $date_parts['mday'] - $offset, $date_parts['year']);

        $return['Mon' . ($longname ? 'day' : '')]    = mktime(0, 0, 0, $date_parts['mon'], ($date_parts['mday'] - $offset), $date_parts['year']);
        $return['Tue' . ($longname ? 'sday' : '')]   = mktime(0, 0, 0, $date_parts['mon'], ($date_parts['mday'] - $offset) + 1, $date_parts['year']);
        $return['Wed' . ($longname ? 'nesday' : '')] = mktime(0, 0, 0, $date_parts['mon'], ($date_parts['mday'] - $offset) + 2, $date_parts['year']);
        $return['Thu' . ($longname ? 'rsday' : '')]  = mktime(0, 0, 0, $date_parts['mon'], ($date_parts['mday'] - $offset) + 3, $date_parts['year']);
        $return['Fri' . ($longname ? 'day' : '')]    = mktime(0, 0, 0, $date_parts['mon'], ($date_parts['mday'] - $offset) + 4, $date_parts['year']);

        #echo '<pre>'; var_dump(self::getDateReadable($start_week_stamp)); echo '</pre>'; #exit;
        #echo '<pre>'; var_dump($return); echo '</pre>'; exit;


        return $return;
    }

    public static function getDateReadable($stamp = false)
    {
        if (!$stamp) {
            $stamp = time();
        }
        return date("Y-m-d H:i:s", $stamp);
    }

    public static function getStartDate()
    {
        $app    = Factory::getApplication();
        $jinput = $app->input;
        $input_date = $jinput->get('date', false);
        #echo '<pre>'; var_dump($start_date); echo '</pre>';
        // Maybe the controller should be checking this and unsetting if invalid:
        if (!$input_date = strtotime($input_date)) {
            $input_date = time();
        }

        $dates = self::getWeekDayTimestamps($input_date);

        return $dates['Monday'];
    }
}