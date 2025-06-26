<?php
/**
 * @package     IBGE\Component\VdEditorIbge\Administrator
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace IBGE\Component\VdEditorIbge\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Dispatcher\DispatcherFactoryInterface;
use Joomla\CMS\Factory;

/**
 * Component VdEditorIbge (Administrator)
 *
 * @since  2.0.0
 */
class VdEditorIbgeComponent extends MVCComponent
{
    /**
     * Constructor.
     *
     * @param   DispatcherFactoryInterface  $dispatcherFactory  The dispatcher factory.
     */
    public function __construct(DispatcherFactoryInterface $dispatcherFactory)
    {
        // Define o nome do componente para o dispatcher.
        // O dispatcher usará este nome para construir os nomes das classes de controller.
        // Ex: 'VirtualDomains' para a view 'virtualdomains' -> VirtualDomainsController
        // O namespace base é 'IBGE\Component\VdEditorIbge\Administrator\Controller'
        parent::__construct($dispatcherFactory, Factory::getApplication()->input);
        $this->setControllerNamespace('IBGE\\Component\\VdEditorIbge\\Administrator\\Controller');
    }
}
