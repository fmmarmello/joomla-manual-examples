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

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * VirtualDomain edit controller class.
 *
 * @since  2.0.0
 */
class VirtualDomainController extends FormController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     */
    protected $text_prefix = 'COM_VDEDITORIBGE_VIRTUALDOMAIN';

    /**
     * The URL view for the edit form.
     *
     * @var    string
     * @since  2.0.0
     */
    protected $view_item = 'virtualdomain'; // Corresponde ao nome da View (sem 'View' ou 'HtmlView')

    /**
     * Class constructor.
     *
     * @param   array                $config   A named array of configuration variables.
     * @param   MVCFactoryInterface  $factory  The factory.
     * @param   mixed                $input    The application input.
     *
     * @since   2.0.0
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, $input = null)
    {
        parent::__construct($config, $factory, $input);
        // O FormController tentará carregar um modelo chamado VirtualDomainModel
        // O nome do modelo é inferido a partir do nome do controller (VirtualDomain)
        // ou pode ser explicitamente definido $this->model_name = 'VirtualDomain';
    }


    /**
     * Method to run batch operations.
     *
     * @param   AdminModel  $model  The model.
     *
     * @return  boolean   True if successful, false otherwise and internal error is set.
     *
     * @since   2.0.0
     */
    public function batch($model = null): bool // Adicionado tipo de retorno
    {
        Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

        // Set the model
        $model = $model ?? $this->getModel('VirtualDomain'); //getModel espera o nome da View/Model

        // Preset the redirect
        $this->setRedirect(Route::_('index.php?option=com_vdeditoribge&view=virtualdomains' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }

    /**
     * Function that allows child controller access to model data
     * after the data has been saved.
     *
     * @param   AdminModel  $model      The data model object.
     * @param   array       $validData  The validated data.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    protected function postSaveHook(AdminModel $model, $validData = array()): void
    {
        parent::postSaveHook($model, $validData);
    }
}
