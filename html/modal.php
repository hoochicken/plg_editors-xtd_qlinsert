<?php
/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2023 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\HTML\HTMLHelper as JHtml;

$base = isset($_GET['bp']) ? urldecode($_GET['bp']) : '';
$token = $_GET['token'] ?? '';
$destination = $_GET['destination'] ?? '';
/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);
require_once(__DIR__ . '/../php/classes/clsPluginEditorsxtdQlinsertHelper.php');
$obj_qlinsertHelper = new clsPluginEditorsxtdQlinsertHelper();
$obj_qlinsertHelper->loadFramework();
$obj_qlinsertHelper->loadLanguageFiles('plg_editors-xtd_qlinsert');

$params = $obj_qlinsertHelper->getPluginParams();
/** @var string $modal_css */
$modal_css = $modal_css ?? '';
$arr = $obj_qlinsertHelper->getFieldArray();
echo HTMLHelper::_('bootstrap.framework');
?>
<!doctype html>
<html>
<head>
    <title>qlinsert Generator</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?= $modal_css; ?>"/>
    <script src="../js/qlinsert.js" type="text/javascript" charset="utf-8"></script>
    <link type="text/css" href="../../../../media/vendor/bootstrap/css/bootstrap.css" rel="stylesheet"/>
    <link type="text/css" href="../../../../media/system/css/joomla-fontawesome.min.css" rel="stylesheet"/>
    <link type="text/css" href="../css/qlinsert.css" rel="stylesheet"/>
    <script>
        function insertQlinsertByTextarea(containerId) {
          let strToBeInserted = document.getElementById(containerId).value;
          window.parent.insertQlinsert('<?= $destination; ?>', strToBeInserted);
        }
        <?php foreach ($arr as $v) echo sprintf('let %s = \'%s\';' . "\n", 'v' . $v, str_replace('\'', "\'", (string)$params->get($v, '')));  ?>
    </script>
</head>
<body>
<div class="container">
    <?php foreach ($arr as $k => $v): ?>
        <textarea style="display:none;" id="container_<?= $k; ?>"><?= $params->get($v, ''); ?></textarea>
    <?php endforeach; ?>
    <h3>qlinsert</h3>
    <ul class="ul-qlinserts">
        <?php
        foreach ($arr as $k => $v):
            $label = $params->get('label' . ucwords((string)$v), '-');
            $label = preg_replace('/[^a-zA-Z0-9_\-\s]/s', '', (string)$label);
            $paramName = $v . 'icon';
            $defaultValue = trim($params->get($v, ''));
            if (empty($defaultValue)) {
                continue;
            }
            $icon = $params->get($paramName, '');
            $label = !empty($icon)
                ? sprintf('<i class="fa %s"></i> ', $icon) . $label
                : $label;
            ?>
            <li><a href="#" class="btn btn-secondary button-well" onclick="insertQlinsertByTextarea('<?= "container_{$k}"; ?>'); if (window.parent.Joomla.Modal) {window.parent.Joomla.Modal.getCurrent().close();}"><?= $label; ?></a> <?= substr(strip_tags((string)$params->get($v, '')), 0, 25); ?>...</li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
