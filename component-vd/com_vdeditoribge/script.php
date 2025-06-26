<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_vdeditoribge
 *
 * @copyright   Copyright (C) 2023 Jules AI. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Script file for VdEditorIbge component
 */
class Com_VdEditorIbgeInstallerScript
{
    /**
     * Method to install the component.
     *
     * @return  boolean  True on success.
     */
    public function install($parent)
    {
        // $parent is the class calling this method.
        // echo '<p>' . JText::_('COM_VDEDITORIBGE_INSTALL_TEXT') . '</p>';
        return true;
    }

    /**
     * Method to uninstall the component.
     *
     * @return  boolean  True on success.
     */
    public function uninstall($parent)
    {
        // echo '<p>' . JText::_('COM_VDEDITORIBGE_UNINSTALL_TEXT') . '</p>';
        return true;
    }

    /**
     * Method to update the component.
     *
     * @param   string  $type    The type of change (upgrade, downgrade, discover_install).
     * @param   object  $parent  The class calling this method.
     *
     * @return  boolean  True on success.
     */
    public function update($parent)
    {
        // $parent is the class calling this method.
        // echo '<p>' . JText::sprintf('COM_VDEDITORIBGE_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
        return true;
    }

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param   string  $type    The type of change (install, update, discover_install, uninstall).
     * @param   object  $parent  The class calling this method.
     *
     * @return  boolean  True on success.
     */
    public function preflight($type, $parent)
    {
        // $parent is the class calling this method.
        // $type is the type of change being made.
        return true;
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param   string  $type    The type of change (install, update, discover_install, uninstall).
     * @param   object  $parent  The class calling this method.
     *
     * @return  boolean  True on success.
     */
    public function postflight($type, $parent)
    {
        // $parent is the class calling this method.
        // $type is the type of change being made.
        return true;
    }
}
