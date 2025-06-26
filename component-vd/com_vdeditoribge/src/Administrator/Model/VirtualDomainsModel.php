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

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Query\QueryInterface; // Import QueryInterface

/**
 * Methods supporting a list of VdEditorIbge records.
 *
 * @since  2.0.0
 */
class VirtualDomainsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     * @since   2.0.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = [
                'id', 'a.id',
                'domain', 'a.domain',
                'home', 'a.home',
                'menuid', 'a.menuid',
                'template', 'a.template',
                'published', 'a.published', // J5 AdminController usa 'state' para published. Adaptar se necessário.
                                           // No entanto, 'published' ainda é comum na tabela.
                'ordering', 'a.ordering'
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      QueryInterface An SQL query object.
     */
    protected function getListQuery(): QueryInterface
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select(
            $this->getState(
                'list.select',
                [
                    'a.id', 'a.domain', 'a.home', 'a.menuid',
                    'a.template', 'a.published', 'a.ordering',
                    'a.checked_out', 'a.checked_out_time', // Necessário para J5 Admin view
                    // Adicionar um alias para o editor do checked_out se necessário para o template
                    // 'uc.name AS editor'
                ]
            )
        );
        $query->from($db->quoteName('#__ibge_virtualdomain') . ' AS a');

        // // Opcional: Join com a tabela de usuários para obter o nome do editor do checked_out
        // $query->select('uc.name AS editor')
        //       ->leftJoin($db->quoteName('#__users') . ' AS uc ON uc.id = a.checked_out');


        // Filter by published state (state in J5)
        $published = $this->getState('filter.published'); // filter_published é o que JHtml::_('jgrid.publishedOptions') usa
        if (is_numeric($published))
        {
            $query->where($db->quoteName('a.published') . ' = ' . (int) $published);
        }
        elseif ($published === '') // Show all items except trashed (if trashed state is -2)
        {
             $query->where($db->quoteName('a.published') . ' IN (0, 1)');
        }
        // Se quisermos incluir lixeira (-2), ajustar aqui.
        // Ex: elseif ($published === '*') $query->where($db->quoteName('a.published') . ' IN (0, 1, -2)');


        // Filter by search in domain
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
            }
            else
            {
                $searchTerm = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where($db->quoteName('a.domain') . ' LIKE ' . $searchTerm);
            }
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.ordering');
        $orderDirn = $this->state->get('list.direction', 'ASC');

        if ($orderCol && $orderDirn)
        {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    /**
     * Method to get the store id based on model configuration state.
     *
     * @param   string  $id  A prefix for the store id.
     * @return  string  A store id.
     * @since   2.0.0
     */
    protected function getStoreId($id = ''): string
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');
        // Adicionar outros filtros ao store id se existirem
        return parent::getStoreId($id);
    }

    /**
     * Overridden method to get the database driver.
     * @return  DatabaseInterface
     * @since   2.0.0
     */
    // protected function getDbo(): DatabaseInterface // ListModel já tem
    // {
    //     return parent::getDbo();
    // }

    /**
      * Populate the FilterForm & list states.
      * ListModel já tem um populateState que lida com filter.search, list.limit, list.start, list.ordering, list.direction.
      * Precisamos adicionar o filter.published.
      */
    protected function populateState($ordering = 'a.ordering', $direction = 'ASC')
    {
        $app = Factory::getApplication();

        // Search filter
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        // Published filter
        // O nome 'filter_published' é o que o layout joomla.searchtools.default espera para o filtro de estado
        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
        $this->setState('filter.published', $published);

        // Outros filtros se houver
        // $this->setState('filter.category_id', $app->input->getInt('filter_category_id', 0));

        parent::populateState($ordering, $direction);
    }
}
```
