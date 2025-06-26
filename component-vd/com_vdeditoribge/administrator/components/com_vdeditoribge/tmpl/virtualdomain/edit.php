<?php
/**
 * @package     IBGE\Component\VdEditorIbge\Administrator
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

if (empty($this->item->id) && Factory::getApplication()->input->getInt('id') > 0) {
    Factory::getApplication()->enqueueMessage(Text::_('COM_VDEDITORIBGE_ERROR_ITEM_NOT_LOADED_IN_VIEW'), 'error');
}

$app = Factory::getApplication();
$input = $app->input;

// Adiciona as strings de texto JS de forma segura para o filter-helper.js
$jsStrings = [
    'COM_VDEDITORIBGE_FILTER_MENU_IDS_PLACEHOLDER' => Text::_('COM_VDEDITORIBGE_FILTER_MENU_IDS_PLACEHOLDER'),
    'COM_VDEDITORIBGE_FILTER_CATEGORY_IDS_PLACEHOLDER' => Text::_('COM_VDEDITORIBGE_FILTER_CATEGORY_IDS_PLACEHOLDER'),
    'COM_VDEDITORIBGE_ERROR_MENUFILTER_INVALID_JSON' => Text::_('COM_VDEDITORIBGE_ERROR_MENUFILTER_INVALID_JSON'),
    'COM_VDEDITORIBGE_ERROR_CATEGORYFILTER_INVALID_JSON' => Text::_('COM_VDEDITORIBGE_ERROR_CATEGORYFILTER_INVALID_JSON'),
    'JGLOBAL_VALIDATION_FORM_FAILED' => Text::_('JGLOBAL_VALIDATION_FORM_FAILED'),
];
HTMLHelper::_('script', 'com_vdeditoribge/administrator/js/filter-helper.js', array('version' => 'auto', 'relative' => true, 'defer' => true));
Factory::getDocument()->addScriptOptions('com_vdeditoribge.strings', $jsStrings);


?>
<form action="<?php echo Route::_('index.php?option=com_vdeditoribge&layout=edit&id=' . (int) ($this->item->id ?? 0)); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

    <?php // echo LayoutHelper::render('joomla.edit.title_alias', $this); // Se tivéssemos alias ?>

    <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true]); ?>

    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_VDEDITORIBGE_FIELDSET_DETAILS_LABEL')); ?>
        <div class="row">
            <div class="col-md-12">
                 <fieldset class="options-form"> <!-- Ou use fieldset com card em J5 -->
                    <?php foreach ($this->form->getFieldset('details') as $field) : ?>
                         <div class="control-group mb-3"> <!-- control-group é mais J3/BS2, J5/BS5 usa form-group ou apenas mb-3 -->
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
            </div>
        </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>

    <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
