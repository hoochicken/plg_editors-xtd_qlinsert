<?php
/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2020 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
$base = urldecode($_GET['bp']);
$token = $_GET['token'];
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
?>
<!doctype html>
<html>
<head>
    <title>qlinsert Generator</title>
    <link rel="stylesheet" href="<?php echo $modal_css; ?>"/>
    <script src="../js/qlinsert.js" type="text/javascript" charset="utf-8"></script>
    <link type="text/css" href="../css/styles.css" rel="stylesheet"/>
</head>
<body>
<div class="container">
    <h3>qlinsert</h3>
    <ul>
        <?php
        $arr = $obj_qlinsertHelper->getFieldArray();
        reset($arr);
        foreach ($arr as $k => $v):
            $label = $params->get('label' . ucwords($v), '-');
            $label = preg_replace('/[^a-zA-Z0-9_\-\s]/s', '', $label);
            ?>
            <li><a href="#" class="btn"
                   onclick="qlinsert('<?php echo $k; ?>','<?php echo $token; ?>')"><?php echo $label; ?></a> <?php echo substr(strip_tags($params->get($v)), 0, 25); ?>
                ...
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
