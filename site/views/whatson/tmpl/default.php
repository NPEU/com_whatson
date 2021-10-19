<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

$table_id = 'whatson-table';

$doc = JFactory::getDocument();
#$doc->include_joomla_scripts = true;

###$doc->addScript("/templates/npeu6/datatables/datatables.min.js");
###$doc->addStyleSheet('/templates/npeu6/datatables/datatables.min.css');

$doc->addScript('/templates/npeu6/js/filter.min.js');

$doc->addStyleSheet('/components/com_whatson/views/whatson/tmpl/whatson.min.css');
$doc->addScript('/components/com_whatson/views/whatson/tmpl/whatson.min.js');
//JHtml::_('behavior.keepalive');
/*
$js = array();
$js[] = 'jQuery(document).ready(function(){';
$js[] = '    jQuery("#' . $table_id . '").DataTable();';
$js[] = '});';

$doc->addScriptDeclaration(implode("\n", $js));
*/

/*
// If you need specific JS/CSS for this view, add them here.
// Example included for DataTables (https://datatables.net/) delete if you don't want this.
// Make sure jQuery is loaded first:
JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');
// Get the doc object:

// Add a script tag with a src:
$doc->addScript("//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js");
#$doc->addScript("//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js");
// Add a CSS link tag:
$doc->addStyleSheet('//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css');
#$doc->addStyleSheet('//cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css');
// Add a script tag with content:
$js = '
jQuery(document).ready(function(){
    jQuery("#' . $table_id . '").DataTable();
});
';
$doc->addScriptDeclaration($js);
*/
#$fieldsets = $this->form->getFieldsets();
#$hidden_fieldset = $this->form->getFieldset('hidden');
$inputs_fieldset = $this->form->getFieldset('inputs');

#echo '<pre>'; var_dump($this->user); echo '</pre>'; #exit;
#echo '<pre>'; var_dump($this->user_profile); echo '</pre>'; #exit;
#echo '<pre>'; var_dump($this->staff); echo '</pre>'; #exit;
#echo '<pre>'; var_dump($_SERVER['SCRIPT_URL']); echo '</pre>'; #exit;


include($_SERVER['DOCUMENT_ROOT'] . '/templates/npeu6/layouts/partial-slimselect.php');

function week_link($start_date, $plus_minus = '+', $user_id = false) {

    $user = JFactory::getUser();
    $new_stamp = strtotime(date('Y-m-d', $start_date) . ' ' . $plus_minus . ' 1 week');

    $current_week_timestamps = WhatsOnHelper::getWeekDayTimestamps();
    if ($new_stamp < $current_week_timestamps['Monday'] && !$user->authorise('core.admin')) {
        return '&nbsp;';
    }

    $return  = '<a href="'. $_SERVER['SCRIPT_URL'];
    $return .= '?date=' . date('Y-m-d', $new_stamp);
    $return .= ($user_id === false) ? '' : '#user-' . $user_id;
    $return .= '" aria-label="' . ($plus_minus == '+' ? 'next' : 'previous') . ' week"';
    $return .= '>';
    $return .= ($plus_minus == '+') ? '&#187;' : '&#171;';
    $return .= '</a>';

    return $return;
}


// 'Custom test' => 'Fiona Alderdice|Benjamin Allin|Noon Altijani'
$whatson_filters = [
    'Show only me' => $this->user->name,
];

// Work out if the TODAY is present on screen:
// (Note this could probably be done in a much better way, but it confuses me)
$today = false;
$showing_week_timestamps = WhatsOnHelper::getWeekDayTimestamps($this->start_date, false);
$current_week_timestamps = WhatsOnHelper::getWeekDayTimestamps(false, false);
if ($showing_week_timestamps['Mon'] == $current_week_timestamps['Mon']) {
    // The presented week is the same as the TODAY week.
    $today = strtolower(date('D'));
}
////

if (!empty($this->user_profile->profile['whatson_filters'])) {
    foreach ($this->user_profile->profile['whatson_filters'] as $filter) {
        $whatson_filters[$filter['whatson_filter_name']] = $filter['whatson_filter_value'];
    }
}

#echo '<pre>'; var_dump($whatson_filters); echo '</pre>'; exit;

?>
<h2>Week beginning <?php echo date('j<\s\u\p>S</\s\u\p> F Y', $this->week_timestamps['Monday']); ?></h2>
<div class="u-text-group u-text-group--push-apart  u-space--below">
        <form method="get" action="<?php echo $_SERVER['SCRIPT_URL']; ?>" class="u-text-group u-text-group--wide-space  u-space--below--none">
            <span class="c-composite">
                <input type="date" name="date" value="<?php echo date('Y-m-d', $this->week_timestamps['Monday']); ?>"<?php if (!$this->user->authorise('core.admin')): ?> min="<?php echo date('Y-m-d', $this->current_week_timestamps['Monday']); ?>"<?php endif; ?>>
                <button class="t-staff-area">Go to date</button>
            </span>
            <?php if ($this->current_week_timestamps['Monday'] != $this->week_timestamps['Monday']) : ?>
            <span> or </span>
            <span><a href="<?php echo $_SERVER['SCRIPT_URL']; ?>" class="c-cta">Back to today</a></span>
            <?php endif; ?>
        </form>
        <p>
            <a href="/user-profile/edit" class="c-cta">Change my WhatsOn preferences</a>
        </p>
</div>
<?php /*<form action="<?php echo $_SERVER['REQUEST_URI']; ?>&task=entry.save" method="post">*/ ?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="return" value="<?php echo base64_encode($this->current_view_uri); ?>" />
    <input type="hidden" name="task" value="entry.save" />
    <?php echo JHtml::_('form.token'); ?>

    <div filterable_group>
        <script type="text/template" filterable_form_template>
            <div class="u-space--below">
                <label for="whatson_filter_staff">Filter staff:</label>
                <select id="whatson_filter_staff" multiple filterable_preset>
                    <?php foreach($this->staff as $staff_member): ?>
                    <option value="<?php echo $staff_member['name']; ?>"><?php echo $staff_member['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php /*<label for="whatson_filter_just_me">Show just me:</label> <input type="checkbox" name="whatson_filter_just_me" id="whatson_filter_just_me" value="<?php echo $this->user->name; ?>" filterable_preset> */?>
                <span class="u-text-group  u-text-group--push-apart">
                    <span id="whatson-filters">
                        <?php foreach ($whatson_filters as $label => $value) : ?>
                        <span class="c-composite">
                            <button class="t-staff-area  whatson-filter-button" value="<?php echo $value ?>"  type="button"><?php echo $label ?></button><?php if ($label != 'Show only me') : ?>
                            <button class="t-staff-area  whatson-filter-button--delete" name="action" value="delete-filter" type="submit" aria-label="Delete filter <?php echo $label ?>">&times;</button><?php endif; ?>
                        </span>
                        <?php endforeach; ?>
                    </span>
                    <span>
                        <span class="c-composite" id="new_filter" hidden>
                            <input type="text" name="new_filter_name" id="new_filter_name">
                            <button type="submit" id="add_new_filter" name="action" value="add-new-filter">Add new filter</button>
                        </span>
                        <button type="reset" filterable_reset class="t-staff-area">Clear filters</button>
                    </span>
                </span>
                <input type="hidden" id="whatson_filter" filterable_input name="whatson_filter">
            </div>
        </script>
        <script type="text/template" filterable_empty_list_template>
            <p filterable_empty_list_message hidden>No matches found.</p>
        </script>


        <table id="<?php echo $table_id; ?>" class="whatson-table  table--sticky-header  t-staff-area" border="1" cellspacing="0" cellpadding="5" role="table" filterable_list>
            <thead>
                <tr role="row">
                    <th role="columnheader"><?php echo week_link($this->start_date, '-'); ?></th>
                    <th role="columnheader">&nbsp;</th>
                    <?php foreach($this->week_timestamps as $day_timestamp): ?>
                    <th role="columnheader"><?php echo date('D j<\s\u\p>S</\s\u\p> F', $day_timestamp); #echo JText::_($field->label); ?></th>
                    <?php endforeach; ?>
                    <th role="columnheader"><?php echo week_link($this->start_date, '+'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($this->staff as $staff_member): ?>
                <?php
                /*
                $view_link = JRoute::_('index.php?option=com_whatson&task=entry.view&id=' . $row->id);
                $edit_link = JRoute::_('index.php?option=com_whatson&task=entry.edit&id=' . $row->id);
                */
                $staff_id = $staff_member['id'];

                // ID's less than 600 are not registered users, they've been added manually.
                // E.g. Events, Visitors.
                if ($staff_id < 600) {
                    // For now assume all users can edit these rows.
                    $can_edit_row = true;
                } else {
                    $is_own_row = false;
                    if ($this->user->authorise('core.edit.own', 'com_whatson') && ($this->user->id == $staff_id)) {
                        $is_own_row = true;
                    }

                    $can_edit_row = $this->can_edit_all;
                    if ($is_own_row) {
                        $can_edit_row = true;
                    }
                }

                $editing_this_row = false;
                if ($can_edit_row && $this->editing_user_row == $staff_id) {
                    $editing_this_row = true;
                }

                ?>
                <tr id="user-<?php echo $staff_id; ?>" role="row" filterable_item>
                    <td role="cell"<?php if ($staff_id < 3) : ?> class="t-warning"<?php endif; ?>><?php echo week_link($this->start_date, '-', $staff_id); ?></td>
                    <td<?php if ($editing_this_row) : ?> class="t-success  is-editing"<?php elseif ($staff_id < 3) : ?> class="t-warning"<?php endif; ?> role="cell">
                        <p class="m-nav" hidden>
                            <?php echo week_link($this->start_date, '-', $staff_id); ?>
                            <?php echo week_link($this->start_date, '+', $staff_id); ?>
                        </p>
                        <p>
                            <span>
                                <span filterable_index><?php echo $staff_member['first_name']; ?></span>
                                <b filterable_index><?php echo $staff_member['last_name']; ?></b>
                            </span>

                            <?php if (!$editing_this_row && $can_edit_row) : ?>
                            <span class="u-space--left--xs">[<a href="<?php echo $_SERVER['SCRIPT_URL']; ?>?date=<?php echo date('Y-m-d', $this->start_date); ?>&edit=<?php echo $staff_id; ?>#user-<?php echo $staff_id; ?>">EDIT</a>]</span>
                            <?php endif; ?>

                            <?php if ($editing_this_row) : ?>

                            <?php if (WhatsOnHelper::getArrayValue($this->items, $staff_id)) : ?>
                            <input type="hidden" name="jform[id]" id="jform_id" value="<?php echo $this->week_timestamps['Monday'] . '.' . $staff_id; ?>">
                            <?php endif; ?>
                            <input type="hidden" name="jform[start_date]" id="jform_start_date" value="<?php echo $this->week_timestamps['Monday']; ?>">
                            <input type="hidden" name="jform[start_date_readable]" id="jform_start_date_readable" value="<?php echo date('Y-m-d H:i:s', $this->week_timestamps['Monday']);; ?>">
                            <input type="hidden" name="jform[user_id]" id="jform_user_id" value="<?php echo $staff_id; ?>">
                            <input type="hidden" name="return" value="<?php echo base64_encode($this->current_view_uri . '#user-' . $staff_id); ?>" />

                            <button type="submit" id="whatson-save" class="u-space--left--xs">Save</button>
                            <a href="<?php echo $_SERVER['SCRIPT_URL']; ?>?date=<?php echo date('Y-m-d', $this->start_date); ?>" class="u-space--left--xs">Cancel</a>
                            <?php endif; ?>
                        </p>
                        <?php if ((!empty($staff_member['tel'])) || (!empty($staff_member['room']))): ?>
                        <p class="c-utilitext  u-text-size--medium">
                            <?php if (!empty($staff_member['tel'])): ?>
                            <svg height="20" width="20" focusable="false" class="icon  u-space--right--xs" aria-hidden="true"><use xlink:href="#icon-phone"></use></svg> <span><?php echo $staff_member['tel']; ?></span>
                            <?php endif; ?>
                            <?php if (!empty($staff_member['room'])): ?>
                            <a href="/staff-area/whos-where#<?php echo strtolower(str_replace('/', '-', $staff_member['room'])); ?>"<?php if (!empty($staff_member['tel'])): ?> class="u-space--left--s"<?php endif; ?>><svg height="20" width="20" focusable="false" class="icon  u-space--right--xs" aria-hidden="true"><use xlink:href="#icon-map-pin"></use></svg> <span><?php echo $staff_member['room']; ?></span></a>
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>
                    </td>
                    <?php foreach($inputs_fieldset as $field): ?>

                    <?php
                    $day = $field->getAttribute('name');
                    ?>

                    <td<?php if ($editing_this_row) : ?> class="t-success  is-editing"<?php elseif ($staff_id < 3) : ?> class="t-warning"<?php endif; ?> data-day="<?php echo ucfirst($field->getAttribute('name')); ?>" role="cell"<?php if ($day == $today) : ?> aria-current="day"<?php endif; ?>>

                        <?php
                        
                        $week  = date('W', $this->week_timestamps['Monday']);
                        $value = WhatsOnHelper::getWhatsOnValue(
                            $this->items,
                            $staff_id,
                            $day,
                            $week
                        );
                        ?>
                        <?php if ($editing_this_row) : ?>
                        <?php
                        $hint = WhatsOnHelper::getWhatsOnPref($staff_id, $day, $week);
                        if (!$hint) {
                            $hint = WhatsOnHelper::getWhatsOnDefault();
                        }
                        $field->hint = $hint;

                        $entry = WhatsOnHelper::getWhatsOnEntry($this->items, $staff_id, $day);
                        if (!empty($entry)) {
                            $field->value = $entry;
                        }
                        ?>
                        <?php echo $field->input; ?>

                        <?php /*<script type="text/template" whatson-input>
                        <?php echo $field->input; ?>
                        </script>*/ ?>
                        <?php else: ?>
                        <?php echo $value; ?>
                        <?php endif; ?>
                    </td>

                    <?php endforeach; ?>
                    <td role="cell"<?php if ($staff_id < 3) : ?> class="t-warning"<?php endif; ?>><?php echo week_link($this->start_date, '+', $staff_id); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</form>
