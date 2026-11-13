<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Boots just enough of Joomla for unit tests: the Joomla library autoloader
 * of a real installation (JOOMLA_ROOT) plus the path constants.
 */

\define('_JEXEC', 1);

$joomlaRoot = rtrim((string) (getenv('JOOMLA_ROOT') ?: \dirname(__DIR__, 4)), '/');

if (!is_file($joomlaRoot . '/libraries/vendor/autoload.php')) {
    fwrite(STDERR, "Joomla installation not found in '$joomlaRoot'. Set JOOMLA_ROOT to a Joomla 6 root.\n");
    exit(1);
}

\define('JPATH_ROOT', $joomlaRoot);
\define('JPATH_BASE', $joomlaRoot . '/administrator');
\define('JPATH_SITE', $joomlaRoot);
\define('JPATH_ADMINISTRATOR', $joomlaRoot . '/administrator');
\define('JPATH_LIBRARIES', $joomlaRoot . '/libraries');
\define('JPATH_PLUGINS', $joomlaRoot . '/plugins');
\define('JPATH_CACHE', sys_get_temp_dir());

require $joomlaRoot . '/libraries/vendor/autoload.php';
require \dirname(__DIR__) . '/vendor/autoload.php';
