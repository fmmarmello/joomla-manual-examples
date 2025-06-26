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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
// HTMLHelper::_('formbehavior.chosen', 'select'); // Menos comum em J5 para filtros de lista

$user            = Factory::getUser();
$listOrder       = $this->escape($this->state->get('list.ordering'));
$listDirn        = $this->escape($this->state->get('list.direction'));
// $saveOrder       = $listOrder === 'a.ordering'; // Reordenação manual não implementada

// if ($saveOrder)
// {
//     $saveOrderingUrl = 'index.php?option=com_vdeditoribge&task=virtualdomains.saveOrderAjax&tmpl=component';
//     HTMLHelper::_('sortablelist.sortable', 'virtualDomainList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
// }
?>

<form action="<?php echo Route::_('index.php?option=com_vdeditoribge&view=virtualdomains'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->filterForm)) : ?>
      <div id="j-search-tools" class="mb-3">
          <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this, 'options' => ['filtersHiddenByDefault' => false]]); ?>
      </div>
    <?php endif; ?>


    <div id="j-main-container" class="j-main-container">
        <?php if (empty($this->items)) : ?>
            <div class="alert alert-info">
                <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-striped table-hover" id="virtualDomainList">
                <caption class="visually-hidden">
                    <?php echo Text::_('COM_VDEDITORIBGE_TABLE_CAPTION_VIRTUALDOMAINS'); ?>
                    <?php if ($listOrder === 'a.ordering') : ?>
                        <span class="icon-arrow-up-3" aria-hidden="true"></span>
                        <?php echo Text::_('JGRID_HEADING_ORDERING'); ?>
                    <?php endif; ?>
                </caption>
                <thead>
                    <tr>
                        <td scope="col" style="width: 1%;" class="text-center">
                            <?php echo HTMLHelper::_('grid.checkall', 'adminForm'); ?>
                        </td>
                        <th scope="col" style="width: 5%;" class="text-center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_VDEDITORIBGE_FIELD_DOMAIN_LABEL', 'a.domain', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" style="width: 10%;" class="d-none d-md-table-cell text-center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_VDEDITORIBGE_FIELD_HOME_LABEL', 'a.home', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" style="width: 10%;" class="d-none d-md-table-cell text-center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_VDEDITORIBGE_FIELD_MENUID_LABEL', 'a.menuid', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" style="width: 15%;" class="d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_VDEDITORIBGE_FIELD_TEMPLATE_LABEL', 'a.template', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" style="width: 5%;" class="d-none d-lg-table-cell text-center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($this->items as $i => $item) :
                    $canEdit        = $user->authorise('core.edit', 'com_vdeditoribge.virtualdomain.' . $item->id);
                    $canEditState   = $user->authorise('core.edit.state', 'com_vdeditoribge.virtualdomain.' . $item->id);
                    $canCheckin     = $user->authorise('core.manage', 'com_checkin') || ($item->checked_out === $user->id || $item->checked_out == 0); // == 0 para inteiros
                    ?>
                    <tr class="row<?php echo $i % 2; ?>" item-id="<?php echo $item->id; ?>">
                        <td class="text-center">
                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="text-center">
                            <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'virtualdomains.', $canEditState, 'cb'); ?>
                        </td>
                        <td>
                            <?php if ($item->checked_out && $item->checked_out != 0) : // Adicionado $item->checked_out != 0
                                $editor = $item->editor ?? Text::_('JUNKNOWN'); // Fallback para editor desconhecido
                                echo HTMLHelper::_('jgrid.checkedout', $i, $editor, $item->checked_out_time, 'virtualdomains.', $canCheckin);
                            endif; ?>
                            <?php if ($canEdit) : ?>
                                <a href="<?php echo Route::_('index.php?option=com_vdeditoribge&task=virtualdomain.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::sprintf('JACTION_EDIT', $this->escape($item->domain)); ?>">
                                    <?php echo $this->escape($item->domain); ?>
                                </a>
                            <?php else : ?>
                                <?php echo $this->escape($item->domain); ?>
                            <?php endif; ?>
                        </td>
                        <td class="d-none d-md-table-cell text-center">
                            <?php echo $item->home ? Text::_('JYES') : Text::_('JNO'); ?>
                        </td>
                        <td class="d-none d-md-table-cell text-center">
                            <?php echo (int) $item->menuid; ?>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <?php echo $this->escape($item->template); ?>
                        </td>
                        <td class="d-none d-lg-table-cell text-center">
                            <?php echo (int) $item->id; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php echo $this->pagination->getListFooter(); ?>
        <?php endif; ?>

        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
