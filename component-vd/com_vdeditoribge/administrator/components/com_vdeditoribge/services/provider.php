<?php
/**
 * @package     IBGE\Component\VdEditorIbge
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace IBGE\Component\VdEditorIbge\Administrator\Service; // Namespace para o provider do admin

defined('_JEXEC') or die;

use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use IBGE\Component\VdEditorIbge\Administrator\Extension\VdEditorIbgeComponent;

/**
 * Service Provider for the VdEditorIbge component (Administrator part)
 *
 * @since  2.0.0
 */
return new class implements ServiceProviderInterface
{
    /**
     * Registers service providers.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     */
    public function register(Container $container): void
    {
        // Registra a classe principal do componente MVC
        // O nome 'com_vdeditoribge.administrator' é uma convenção.
        // O Joomla usará isso para encontrar o componente quando o dispatcher for chamado.
        $container->set(
            ComponentInterface::class, // Ou VdEditorIbgeComponent::class se quisermos ser mais específicos
            function (Container $container) {
                $component = new VdEditorIbgeComponent($container->get('DispatcherFactory'));
                // A classe VdEditorIbgeComponent já define o namespace do controller no construtor.
                return $component;
            }
        );

        // Registra fábricas MVC para este componente (Models, Views, Tables)
        // O namespace fornecido é o base para Models, Views, Tables dentro do Administrator.
        $container->registerServiceProvider(new MVCFactory('\\IBGE\\Component\\VdEditorIbge\\Administrator'));

        // Registra a fábrica do dispatcher do componente
        // O primeiro parâmetro é o namespace do Administrator do componente.
        // O segundo é o Fully Qualified Class Name (FQCN) da classe de extensão principal do componente.
        $container->registerServiceProvider(new ComponentDispatcherFactory(
            '\\IBGE\\Component\\VdEditorIbge\\Administrator',
            VdEditorIbgeComponent::class
        ));
    }
};
