<?php
/**
 * @package     IBGE\Component\VdEditorIbge\Administrator
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace IBGE\Component\VdEditorIbge\Administrator\View\VirtualDomains; // Namespace correto

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper; // Para permissões
use Joomla\CMS\Pagination\Pagination; // Para type hint
use Joomla\CMS\Object\CMSObject; // Para type hint de $state
use Joomla\CMS\Form\Form; // Para type hint de $filterForm

/**
 * View to display a list of Virtual Domains.
 *
 * @since  2.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected ?array $items = null;
    protected ?Pagination $pagination = null;
    protected ?CMSObject $state = null; // Geralmente um RegistryObject ou DataObject
    protected ?Form $filterForm = null;
    protected ?array $activeFilters = null;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     * @return  void
     */
    public function display($tpl = null): void
    {
        $this->items          = $this->get('Items');
        $this->pagination     = $this->get('Pagination');
        $this->state          = $this->get('State');
        $this->filterForm     = $this->get('FilterForm');
        $this->activeFilters  = $this->get('ActiveFilters');

        if (count($errors = $this->get('Errors')))
        {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            // return; // Não retornar para que o usuário veja a interface parcialmente
        }

        $this->addToolBar();

        // Se os templates estiverem em administrator/tmpl/[viewname]/[layout].php, o Joomla deve encontrá-los.
        // $this->setLayoutPath(JPATH_COMPONENT_ADMINISTRATOR . '/tmpl/' . strtolower($this->getName()));
        // $this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR . '/tmpl/' . strtolower($this->getName()));


        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     * @since   2.0.0
     */
    protected function addToolBar(): void
    {
        // As permissões em J5 podem ser verificadas com base no nome do componente e na ação.
        // $this->state->get('filter.category_id') não é relevante aqui.
        $canDo = ContentHelper::getActions('com_vdeditoribge');
        $user  = Factory::getUser();

        ToolbarHelper::title(Text::_('COM_VDEDITORIBGE_MANAGER_VIRTUALDOMAINS_J5'), 'list-ul');

        if ($canDo->get('core.edit') || $canDo->get('core.edit.own'))
        {
            ToolbarHelper::editList('virtualdomain.edit', 'JTOOLBAR_EDIT');
        }

        if ($canDo->get('core.edit.state'))
        {
            ToolbarHelper::publish('virtualdomains.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('virtualdomains.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            // Não temos lixeira/arquivamento neste componente por enquanto.
            // ToolbarHelper::archiveList('virtualdomains.archive', 'JTOOLBAR_ARCHIVE');
            // if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
            //    ToolbarHelper::deleteList(Text::_('COM_VDEDITORIBGE_CONFIRM_DELETE'), 'virtualdomains.delete', 'JTOOLBAR_EMPTY_TRASH');
            // } elseif ($canDo->get('core.edit.state')) {
            //    ToolbarHelper::trash('virtualdomains.trash', 'JTOOLBAR_TRASH');
            // }
        }

        if ($user->authorise('core.admin', 'com_vdeditoribge') || $user->authorise('core.options', 'com_vdeditoribge'))
        {
            ToolbarHelper::preferences('com_vdeditoribge');
        }
    }

    /**
     * Returns an array of fields the table can be sorted by
     * @return  array  Array containing the field name to sort by as the key and display text as value
     * @since   2.0.0
     */
    protected function getSortFields(): array
    {
        return [
            'a.ordering' => Text::_('JGRID_HEADING_ORDERING'),
            'a.published' => Text::_('JSTATUS'),
            'a.domain' => Text::_('COM_VDEDITORIBGE_FIELD_DOMAIN_LABEL'),
            'a.home' => Text::_('COM_VDEDITORIBGE_FIELD_HOME_LABEL'),
            'a.menuid' => Text::_('COM_VDEDITORIBGE_FIELD_MENUID_LABEL'),
            'a.id' => Text::_('JGRID_HEADING_ID')
        ];
    }
}
