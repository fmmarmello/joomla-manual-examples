<?php
/**
 * @package     IBGE\Component\VdEditorIbge
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace IBGE\Component\VdEditorIbge\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Dispatcher\DispatcherFactoryInterface;
use Joomla\CMS\Dispatcher\DispatcherInterface; // Added for getController return type
use Psr\Container\ContainerInterface; // Added for createDispatcher type hint

/**
 * VdEditorIbge Component Administrator Entry Point
 *
 * @since  2.0.0
 */
class VdeditoribgeComponent extends MVCComponent implements ComponentInterface
{
    /**
     * Constructor.
     *
     * @param   DispatcherFactoryInterface  $dispatcherFactory  The dispatcher factory.
     */
    public function __construct(DispatcherFactoryInterface $dispatcherFactory)
    {
        parent::__construct($dispatcherFactory);

        // Set the controller namespace
        $this->namespace = 'IBGE\\Component\\VdEditorIbge\\Administrator\\Controller';
    }

    /**
     * Get the dispatcher for the component.
     *
     * @param   ContainerInterface  $container  The DI container.
     *
     * @return  DispatcherInterface  The dispatcher.
     *
     * @since   4.0.0
     */
    protected function createDispatcher(ContainerInterface $container): DispatcherInterface
    {
        return $this->dispatcherFactory->createDispatcher($this);
    }

    /**
     * Get the controller instance.
     *
     * @param   string  $name     The name of the controller.
     * @param   string  $prefix   The class prefix.
     * @param   array   $config   An array of optional key/value settings.
     *
     * @return  object|false  A controller object or false on failure.
     *
     * @since   1.6  (This method signature is from older Joomla versions but retained for compatibility if needed, though MVCComponent handles it)
     */
    public function getController($name = 'display', $prefix = 'Administrator', $config = [])
    {
        // MVCComponent's getController is generally sufficient.
        // This override is here if specific logic is needed in the future,
        // but for now, it can just call the parent or be removed if not customizing.
        return parent::getController($name, $prefix, $config);
    }
}
