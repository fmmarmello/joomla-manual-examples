<?php
/**
 * @package     IBGE\Component\VdEditorIbge\Administrator
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace IBGE\Component\VdEditorIbge\Administrator\View\VirtualDomain; // Namespace correto

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper; // Para permissões
use Joomla\CMS\Form\Form; // Para type hint
use Joomla\CMS\Object\CMSObject; // Para type hint de $item

/**
 * View to edit a Virtual Domain.
 *
 * @since  2.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected ?Form $form = null; // Adicionado tipo
    protected ?CMSObject $item = null; // Adicionado tipo, CMSObject é uma classe base para itens
    protected ?CMSObject $state = null; // Adicionado tipo
    // protected $canDo; // Pode ser obtido no display ou no template

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automaticamente searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null): void
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');
        // $this->canDo = ContentHelper::getActions($this->state->get('component.name'), 'virtualdomain', $this->item->id ?? 0);

        if (!$this->item && Factory::getApplication()->input->getInt('id', 0)) {
             Factory::getApplication()->enqueueMessage(Text::_('COM_VDEDITORIBGE_ERROR_ITEM_NOT_FOUND_OR_LOADED'), 'error');
        }
        if (!$this->form) {
             Factory::getApplication()->enqueueMessage(Text::_('COM_VDEDITORIBGE_ERROR_FORM_NOT_LOADED_IN_VIEW'), 'error');
        }


        if (count($errors = $this->get('Errors')))
        {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            // return; // Pode ser melhor não retornar para que o usuário veja o formulário parcialmente
        }

        $this->addToolBar();

        // Define o caminho dos templates para a view.
        // J5 MVC HtmlView procura em administrator/tmpl/[viewname]/[layout].php
        // e depois em components/com_example/tmpl/[viewname]/[layout].php
        // Se os templates estão em administrator/tmpl/[viewname], isso deve funcionar.
        // Se for necessário, descomente e ajuste os caminhos.
        // $this->setLayoutPath(JPATH_COMPONENT_ADMINISTRATOR . '/tmpl/' . strtolower($this->getName()));
        // $this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR . '/tmpl/' . strtolower($this->getName()));


        parent::display($tpl);
        $this->addScripts();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   2.0.0
     */
    protected function addToolBar(): void
    {
        $app   = Factory::getApplication();
        $input = $app->input;
        $input->set('hidemainmenu', true); // Ainda relevante em J5

        $user  = Factory::getUser();
        // $isNew = ($this->item && $this->item->id == 0); // Não deve acontecer

        $canDo = ContentHelper::getActions('com_vdeditoribge', 'virtualdomain', $this->item->id ?? 0);

        ToolbarHelper::title(
            Text::_('COM_VDEDITORIBGE_MANAGER_VIRTUALDOMAIN_EDIT_J5'),
            'pencil-alt icon-edit'
        );

        if ($canDo->get('core.edit'))
        {
            ToolbarHelper::apply('virtualdomain.apply', 'JTOOLBAR_APPLY');
            ToolbarHelper::save('virtualdomain.save', 'JTOOLBAR_SAVE');
        }

        ToolbarHelper::cancel('virtualdomain.cancel', 'JTOOLBAR_CLOSE');
    }

    /**
     * Add a script to the view.
     * @return  void
     * @since   2.0.0
     */
    protected function addScripts(): void
    {
        // O script JS para filtros já está no tmpl/edit.php.
        // Se houver scripts específicos da view que não estão no template:
        // $document = Factory::getDocument();
        // $document->addScriptDeclaration("console.log('HtmlView VirtualDomain scripts loaded');");
    }
}
