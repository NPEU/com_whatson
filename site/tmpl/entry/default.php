<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_whatson
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */


#use Joomla\CMS\Factory;
#use Joomla\CMS\Language\Multilanguage;
#use Joomla\CMS\Language\Text;
#use Joomla\CMS\Layout\FileLayout;
#use Joomla\CMS\Layout\LayoutHelper;
#use Joomla\CMS\Router\Route;
#use Joomla\CMS\Session\Session;
#use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

$skip = array(
    'id',
    'title'
);
?>
<?php foreach ($this->form->getFieldsets() as $name => $fieldset): ?>
<?php /*<h2><?php echo Text::_($fieldset->label); ?></h2>*/ ?>
<dl>
    <?php foreach ($this->form->getFieldset($name) as $field): if(!in_array($field->fieldname, $skip)): ?>
    <dt><?php echo Text::_($field->getAttribute('label')); ?></dt>
    <dd><?php echo $field->value; ?></dd>
    <?php endif; endforeach; ?>
</dl>
<?php endforeach; ?>
<p>
    <a href="<?php echo Route::_('index.php?option=com_whatson&view=entry&task=entry.edit&id=' . $this->item->id); ?>">
        <?php echo Text::_('COM_WHATSON_RECORDS_ACTION_EDIT'); ?>
    </a>
</p>
<p>
    <a href="<?php echo Route::_('index.php?option=com_whatson'); ?>">Back</a>
</p>
