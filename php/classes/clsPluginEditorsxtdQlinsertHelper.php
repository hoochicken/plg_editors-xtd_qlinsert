<?php
/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright    Copyright (C) 2023 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

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
        // Set profiler start time and memory usage and mark afterLoad in the profiler.
        JDEBUG && \Joomla\CMS\Profiler\Profiler::getInstance('Application')->setStart($startTime, $startMem)->mark('afterLoad');

        // Boot the DI container
        $container = \Joomla\CMS\Factory::getContainer();

        /*
         * Alias the session service keys to the web session service as that is the primary session backend for this application
         *
         * In addition to aliasing "common" service keys, we also create aliases for the PHP classes to ensure autowiring objects
         * is supported.  This includes aliases for aliased class names, and the keys for aliased class names should be considered
         * deprecated to be removed when the class name alias is removed as well.
         */
        $container->alias('session.web', 'session.web.site')
            ->alias('session', 'session.web.site')
            ->alias('JSession', 'session.web.site')
            ->alias(\Joomla\CMS\Session\Session::class, 'session.web.site')
            ->alias(\Joomla\Session\Session::class, 'session.web.site')
            ->alias(\Joomla\Session\SessionInterface::class, 'session.web.site');

        // Instantiate the application.
        $app = $container->get(\Joomla\CMS\Application\SiteApplication::class);

        // Set the application as global app
        \Joomla\CMS\Factory::$application = $app;

        // Execute the application.
        // $app->execute;
        $app->loadDocument();
        /** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
        $wa = $app->getDocument()->getWebAssetManager();
        $wa->registerAndUseStyle('qlinsert-font-awesome', 'joomla-fontawesome.min.css');
    }

    function getPluginParams()
    {
        $this->plgParams = new Registry();
        $plugin = PluginHelper::getPlugin('editors-xtd', 'qlinsert');
        if ($plugin && isset($plugin->params)) $this->plgParams->loadString($plugin->params);
        else die(Text::_('PLUGIN'));
        return $this->plgParams;
    }

    function loadLanguageFiles($extension)
    {
        $lang = Factory::getLanguage();
        $base_dir = JPATH_SITE;
        $reload = true;
        $lang->load($extension, $base_dir, $lang->getTag(), $reload);
    }

    function getFieldArray()
    {
        return ['default', 'insert1', 'insert2', 'insert3', 'insert4', 'insert5', 'insert6', 'insert7', 'insert8', 'insert9', 'insert10',];
    }
}
