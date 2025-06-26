<?php
/**
 * @package     IBGE\Component\VdEditorIbge\Administrator
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace IBGE\Component\VdEditorIbge\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory; // Se precisar de Factory para algo, como User

/**
 * VdEditorIbge Table class for #__ibge_virtualdomain.
 *
 * @since  2.0.0
 */
class VirtualDomainTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseInterface  $db  A database connector object
     */
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__ibge_virtualdomain', 'id', $db);
        // J5 usa 'state' para published em alguns contextos, mas a coluna da tabela é 'published'.
        // Se o Table class precisar mapear 'state' para 'published' automaticamente:
        // $this->setColumnAlias('state', 'published');
    }

    /**
     * Method to perform sanity checks on the instance properties before storing to the database.
     *
     * @return  boolean  True if the instance is sane and able to be stored.
     * @since   2.0.0
     */
    public function check(): bool
    {
        // Assegura que o 'domain' não está vazio
        if (trim((string) $this->domain) === '')
        {
            $this->setError(Text::_('COM_VDEDITORIBGE_VALIDATION_ERROR_DOMAIN_REQUIRED'));
            return false;
        }

        // Validação do JSON em params (básica, o Model faz a mais complexa)
        if (!empty($this->params)) {
            json_decode($this->params);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Não vamos definir erro aqui para não duplicar mensagens do Model,
                // a menos que queiramos um log específico da Table.
                // $this->setError(Text::sprintf('COM_VDEDITORIBGE_VALIDATION_ERROR_PARAMS_INVALID_JSON_TABLE', json_last_error_msg()));
                // return false; // Impediria o salvamento se o JSON estiver quebrado.
            }
        }

        // Normalizar datas vazias para o formato de data nula do banco de dados
        // Garante que getDbo() é chamado no objeto Table atual.
        $db = $this->getDbo();
        if (empty($this->checked_out_time) || $this->checked_out_time === $db->getNullDate())
        {
            $this->checked_out_time = $db->getNullDate();
        }

        // Validações para campos numéricos e booleanos (0/1)
        $this->home      = (int) $this->home;
        $this->menuid    = (int) $this->menuid;
        $this->template_style_id = (int) $this->template_style_id;
        $this->viewlevel = (int) $this->viewlevel;
        $this->published = (int) $this->published; // J5 pode usar -2 para lixeira, 0 não publicado, 1 publicado

        if (!in_array($this->home, [0, 1])) {
            $this->home = 0;
        }
        // Para published, o Joomla pode usar 0, 1, -2 (lixeira).
        // A validação exata pode depender se a lixeira está habilitada para o componente.
        // Por agora, vamos permitir os valores comuns.
        // if (!in_array($this->published, [0, 1, -2])) {
        // $this->published = 0;
        // }

        return parent::check();
    }

    /**
     * Method to store a row in the database from the Table instance properties.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     * @return  boolean  True on success.
     * @since   2.0.0
     */
    public function store($updateNulls = false): bool
    {
        // A lógica para prevenir a criação de novos itens está no Model.
        return parent::store($updateNulls);
    }

    // publish e unpublish são geralmente tratados pelo Model (AdminModel)
    // ou pelos controllers (AdminController) que chamam o Table::publish().
    // A classe Table base já tem um método publish().
}
