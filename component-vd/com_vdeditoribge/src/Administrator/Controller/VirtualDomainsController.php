<?php
/**
 * @package     IBGE\Component\VdEditorIbge\Administrator
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace IBGE\Component\VdEditorIbge\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

/**
 * VirtualDomains list controller class.
 * Controller para a lista de VirtualDomains.
 *
 * @since  2.0.0
 */
class VirtualDomainsController extends AdminController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var string
     */
    protected $text_prefix = 'COM_VDEDITORIBGE_VIRTUALDOMAINS';

    /**
     * The URL view list.
     *
     * @var    string
     * @since  2.0.0
     */
    protected $view_list = 'virtualdomains'; // Corresponde ao nome da View (sem 'View' ou 'HtmlView')

    /**
     * Constructor.
     *
     * @param   array                $config   An optional associative array of configuration settings.
     * @param   MVCFactoryInterface  $factory  The factory.
     * @param   mixed                $input    The application input.
     *
     * @since   2.0.0
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, $input = null)
    {
        parent::__construct($config, $factory, $input);
        // O AdminController espera que o modelo seja nomeado como o controller + 'Model' (VirtualDomainsModel)
        // ou pode ser definido em $this->model_name
    }

    /**
     * Proxy for getModel.
     * O AdminController do Joomla 5 normalmente lida bem com a obtenção do modelo correto
     * se as convenções de nomenclatura forem seguidas (VirtualDomainsModel para VirtualDomainsController).
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional (geralmente o namespace do Model).
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  \Joomla\CMS\MVC\Model\ListModel|\Joomla\CMS\MVC\Model\BaseDatabaseModel|false
     */
    public function getModel($name = 'VirtualDomains', $prefix = '', $config = ['ignore_request' => true])
    {
        // Se o prefixo não for fornecido, o AdminController tentará o namespace do componente.
        // Para garantir, podemos especificar o namespace completo do Model aqui se necessário.
        // $prefix = $prefix ?: 'IBGE\\Component\\VdEditorIbge\\Administrator\\Model';
        return parent::getModel($name, $prefix, $config);
    }
}
