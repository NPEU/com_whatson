<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

$table_id = 'whatsonTable';
/*
// If you need specific JS/CSS for this view, add them here.
// Example included for DataTables (https://datatables.net/) delete if you don't want this.
// Make sure jQuery is loaded first:
JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');
// Get the doc object:
$doc = JFactory::getDocument();
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

#echo '<pre>'; var_dump($this->staff); echo '</pre>'; #exit;
#echo '<pre>'; var_dump($_SERVER['SCRIPT_URL']); echo '</pre>'; #exit;

function week_link($start_date, $plus_minus = '+', $user_id = false) {

    $user = JFactory::getUser();
    $new_stamp = strtotime(date('Y-m-d', $start_date) . ' ' . $plus_minus . ' 1 week');
    
    $current_week_stamps = WhatsOnHelper::getWeekDayTimestamps();
    if ($new_stamp < $current_week_stamps['Monday'] && !$user->authorise('core.admin')) {
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

?>
<h2>Week beginning <?php echo date('j<\s\u\p>S</\s\u\p> F Y', $this->week_timestamps['Monday']); ?></h2>
<?php /*
<table aria-hidden="true" class="whatson-table">
    <thead>
        <tr>
            <th><?php echo week_link($this->start_date, '-'); ?></th>
            <th>&nbsp;</th>
            <?php foreach($this->week_timestamps as $day_timestamp): ?>
            <th><?php echo date('D j<\s\u\p>S</\s\u\p> F', $day_timestamp); #echo JText::_($field->label); ?></th>
            <?php endforeach; ?>
            <th><?php echo week_link($this->start_date, '+'); ?></th>
        </tr>
    </thead>
</table>
*/ ?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>&task=entry.save" method="post">
    <div class="table-scroll-wrap">
        
        <table id="<?php echo $table_id; ?>" class="whatson-table">
            <thead>
                <tr>
                    <th><?php echo week_link($this->start_date, '-'); ?></th>
                    <th>&nbsp;</th>
                    <?php foreach($this->week_timestamps as $day_timestamp): ?>
                    <th><?php echo date('D j<\s\u\p>S</\s\u\p> F', $day_timestamp); #echo JText::_($field->label); ?></th>
                    <?php endforeach; ?>
                    <th><?php echo week_link($this->start_date, '+'); ?></th>
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
                
                $is_own_row = false;
                if ($this->user->authorise('core.edit.own', 'com_whatson') && ($this->user->id == $staff_id)) {
                    $is_own_row = true;
                }

                $can_edit_row = $this->can_edit_all;
                if ($is_own_row) {
                    $can_edit_row = true;
                }
                
                $editing_this_row = false;
                if ($can_edit_row && $this->editing_user_row == $staff_id) {
                    $editing_this_row = true;
                }

                ?>
                <tr id="user-<?php echo $staff_id; ?>">
                    <td><?php echo week_link($this->start_date, '-', $staff_id); ?></td>
                    <td<?php if ($editing_this_row) : ?> class="c-system-message  t-success"<?php endif; ?>>
                        <p>
                            <span><?php echo $staff_member['first_name']; ?></span>
                            <b><?php echo $staff_member['last_name']; ?></b>
                            
                            <?php if (!$editing_this_row && $can_edit_row) : ?>
                            <span>[<a href="<?php echo $_SERVER['SCRIPT_URL']; ?>?date=<?php echo date('Y-m-d', $this->start_date); ?>&edit=<?php echo $staff_id; ?>#user-<?php echo $staff_id; ?>">EDIT</a>]</span>
                            <?php endif; ?>
                            
                            <?php if ($editing_this_row) : ?>
                            
                            <?php if (WhatsOnHelper::getArrayValue($this->items, $staff_id)) : ?>
                            <input type="hidden" name="jform[id]" id="jform_id" value="<?php echo $this->week_timestamps['Monday'] . '.' . $staff_id; ?>"> 
                            <?php endif; ?>
                            <input type="hidden" name="jform[start_date]" id="jform_start_date" value="<?php echo $this->week_timestamps['Monday']; ?>">
                            <input type="hidden" name="jform[start_date_readable]" id="jform_start_date_readable" value="<?php echo date('Y-m-d H:i:s', $this->week_timestamps['Monday']);; ?>">
                            <input type="hidden" name="jform[user_id]" id="jform_user_id" value="<?php echo $staff_id; ?>">
                            <input type="hidden" name="return" value="<?php echo base64_encode($this->current_view_uri . '#user-' . $staff_id); ?>" />
                            <?php echo JHtml::_('form.token'); ?>
                            
                            <button type="submit" name="task" id="whatson-save" value="entry.save">Save</button>
                            <?php endif; ?>
                        </p>
                        <?php if ((!empty($staff_member['tel'])) || (!empty($staff_member['room']))): ?>
                        <p>
                            <?php if (!empty($staff_member['tel'])): ?>
                            <span><?php echo $staff_member['tel']; ?></span>
                            <?php endif; ?>
                            <?php if (!empty($staff_member['room'])): ?>
                            <span><?php echo $staff_member['room']; ?></span>
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>
                    </td>
                    <?php foreach($inputs_fieldset as $field): ?>
                    
                    <td<?php if ($editing_this_row) : ?> class="c-system-message  t-success"<?php endif; ?>>       
                        
                        <?php 
                        $day   = $field->getAttribute('name');
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
                    <td><?php echo week_link($this->start_date, '+', $staff_id); ?></td>
                </tR>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</form>