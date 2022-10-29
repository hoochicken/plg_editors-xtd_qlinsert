<?php
/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright    Copyright (C) 2020 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class clsPluginEditorsxtdQlinsertHelper
{
    public $plgParams;

    function __construct()
    {

    }

    function loadFramework()
    {
        $dir = __DIR__ . '/../../../../..';
        if (file_exists($dir . '/defines.php')) include_once $dir . '/defines.php';
        if (!defined('_JDEFINES')) {
            define('JPATH_BASE', $dir);
            require_once JPATH_BASE . '/includes/defines.php';
        }
        require_once JPATH_BASE . '/includes/framework.php';
    }

    function getPluginParams()
    {
        $this->plgParams = new JRegistry();
        $plugin = JPluginHelper::getPlugin('editors-xtd', 'qlinsert');
        if ($plugin && isset($plugin->params)) $this->plgParams->loadString($plugin->params);
        else die(JText::_('PLUGIN'));
        return $this->plgParams;
    }

    function loadLanguageFiles($extension)
    {
        $lang = JFactory::getLanguage();
        $base_dir = JPATH_SITE;
        $reload = true;
        $lang->load($extension, $base_dir, $lang->getTag(), $reload);
    }

    function getFieldArray()
    {
        return ['default', 'insert1', 'insert2', 'insert3', 'insert4',];
    }

    function cleanString($str)
    {
        return $str;
    }
}
