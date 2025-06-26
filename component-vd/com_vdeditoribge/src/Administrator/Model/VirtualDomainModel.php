<?php
/**
 * @package     IBGE\Component\VdEditorIbge\Administrator
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace IBGE\Component\VdEditorIbge\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\TableInterface;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Application\ApplicationHelper; // Para stringURLSafe, se usado

/**
 * VdEditorIbge Model for an Individual Virtual Domain.
 *
 * @since  2.0.0
 */
class VirtualDomainModel extends AdminModel
{
    /**
     * The prefix to use with controller messages.
     * @var string
     */
    protected $text_prefix = 'COM_VDEDITORIBGE_VIRTUALDOMAIN';

    /**
     * O tipo de item para este modelo.
     * @var    string
     * @since  2.0.0
     */
    public $typeAlias = 'com_vdeditoribge.virtualdomain';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $name    The table name. Optional.
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $options Configuration array for model. Optional.
     * @return  TableInterface A database object
     * @since   2.0.0
     */
    public function getTable($name = 'VirtualDomain', $prefix = '', $options = []): TableInterface
    {
        return parent::getTable($name, 'IBGE\\Component\\VdEditorIbge\\Administrator\\Table', $options);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     * @return  Form|null    A Form object on success, null on failure
     * @since   2.0.0
     */
    public function getForm($data = [], $loadData = true): ?Form
    {
        // AdminModel (parent class) should look for the form in administrator/components/com_vdeditoribge/forms/
        // if the XML file is named virtualdomain.xml.
        $options = ['control' => 'jform', 'load_data' => $loadData];
        $form = $this->loadForm(
            $this->typeAlias, // e.g., com_vdeditoribge.virtualdomain
            'virtualdomain',  // XML file name (virtualdomain.xml)
            $options
        );

        if (empty($form))
        {
            $this->setError(Text::_('COM_VDEDITORIBGE_ERROR_FORM_NOT_LOADED'));
            return null;
        }
        return $form;
    }

    /**
     * Method to get the data that should be injected into the form.
     * @return  mixed  The data for the form.
     * @since   2.0.0
     */
    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState($this->typeAlias . '.edit.data', null);

        if ($data === null)
        {
            $data = $this->getItem();
        }

        if (!$data) { // If getItem() returns false or null
            $data = new \stdClass(); // Avoid errors if item not found / new item (though we block new)
        }


        if (is_object($data) && property_exists($data, 'params') && is_string($data->params)) {
            try {
                $paramsRegistry = new Registry($data->params); // Constructor accepts JSON string
                $paramsData = $paramsRegistry->toArray();

                $data->menufilter_json = isset($paramsData['menufilter']) ? json_encode($paramsData['menufilter'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '[]';
                $data->categoryfilter_json = isset($paramsData['categoryfilter']) ? json_encode($paramsData['categoryfilter'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '[]';

            } catch (\Exception $e) {
                $app->enqueueMessage(Text::sprintf('COM_VDEDITORIBGE_ERROR_PARAMS_PARSE_FAILED', $e->getMessage()), 'error');
                $data->menufilter_json = '[]';
                $data->categoryfilter_json = '[]';
            }
        } elseif (is_object($data)) { // Ensure properties exist even if params is not there or not a string
             $data->menufilter_json = $data->menufilter_json ?? '[]'; // Use null coalescing operator
             $data->categoryfilter_json = $data->categoryfilter_json ?? '[]';
        } else { // Fallback if $data is not an object for some reason
            $data = new \stdClass(); // Ensure $data is an object
            $data->menufilter_json = '[]';
            $data->categoryfilter_json = '[]';
        }

        // $this->preprocessData($this->typeAlias, $data); // Deprecated in J5 for user state data

        return $data;
    }

    /**
     * Prepare and sanitise the table data prior to saving.
     * @param   TableInterface  $table  A TableInterface object.
     * @return  void
     * @since   2.0.0
     */
    protected function prepareTable(TableInterface $table): void
    {
        $app = Factory::getApplication();
        $input = $app->input;
        $jform_data = $input->post->get('jform', [], 'array');


        $menufilter_json = $jform_data['menufilter_json'] ?? '[]';
        $categoryfilter_json = $jform_data['categoryfilter_json'] ?? '[]';

        $menuFilterArray = json_decode($menufilter_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $app->enqueueMessage(Text::sprintf('COM_VDEDITORIBGE_ERROR_MENUCATEGORYFILTER_INVALID_JSON_MENU', json_last_error_msg()), 'error');
            $menuFilterArray = [];
        }

        $categoryFilterArray = json_decode($categoryfilter_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $app->enqueueMessage(Text::sprintf('COM_VDEDITORIBGE_ERROR_MENUCATEGORYFILTER_INVALID_JSON_CATEGORY', json_last_error_msg()), 'error');
            $categoryFilterArray = [];
        }

        $currentParams = [];
        if (!empty($table->id) && !empty($table->params) && is_string($table->params)) {
             try {
                $paramsRegistry = new Registry($table->params);
                $currentParams = $paramsRegistry->toArray();
             } catch (\Exception $e) {
                $app->enqueueMessage(Text::sprintf('COM_VDEDITORIBGE_WARNING_EXISTING_PARAMS_INVALID', $table->id), 'warning');
             }
        }

        $newParamsData = $currentParams;
        $newParamsData['menufilter'] = $menuFilterArray;
        $newParamsData['categoryfilter'] = $categoryFilterArray;

        $table->params = json_encode($newParamsData);
    }

    /**
     * Method to save the form data.
     * @param   array  $data  The form data.
     * @return  boolean  True on success.
     * @since   2.0.0
     */
    public function save($data)
    {
        $app = Factory::getApplication();
        if (empty($data['id'])) {
            $app->enqueueMessage(Text::_('COM_VDEDITORIBGE_ERROR_CANNOT_CREATE_NEW_ITEM'), 'error');
            $this->setError(Text::_('COM_VDEDITORIBGE_ERROR_CANNOT_CREATE_NEW_ITEM'));
            return false;
        }

        $menufilter_json = $data['menufilter_json'] ?? '[]';
        $categoryfilter_json = $data['categoryfilter_json'] ?? '[]';

        json_decode($menufilter_json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $app->enqueueMessage(Text::sprintf('COM_VDEDITORIBGE_ERROR_MENUCATEGORYFILTER_INVALID_JSON_MENU_PREP', json_last_error_msg()), 'error');
            $this->setError(Text::_('COM_VDEDITORIBGE_ERROR_MENUCATEGORYFILTER_INVALID_JSON_MENU_PREP'));
            return false;
        }
        json_decode($categoryfilter_json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $app->enqueueMessage(Text::sprintf('COM_VDEDITORIBGE_ERROR_MENUCATEGORYFILTER_INVALID_JSON_CATEGORY_PREP', json_last_error_msg()), 'error');
            $this->setError(Text::_('COM_VDEDITORIBGE_ERROR_MENUCATEGORYFILTER_INVALID_JSON_CATEGORY_PREP'));
            return false;
        }

        return parent::save($data);
    }

    /** @return false */
    protected function allowSaveAndNew(): bool
    {
        return false;
    }

    /** @return false */
    protected function allowSaveAsCopy(): bool
    {
        return false;
    }
}
