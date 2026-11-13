<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

/** @var object[] $inserts  each with ->content, ->label, ->icon */

$e = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>
<div class="container-fluid qlinsert-modal">
    <?php if (!$inserts): ?>
        <div class="alert alert-info"><?= Text::_('PLG_EDITORS-XTD_QLINSERT_MSG_NOVALUESDEFINED') ?></div>
    <?php else: ?>
        <ul class="ul-qlinserts">
            <?php foreach ($inserts as $k => $insert): ?>
                <li>
                    <textarea hidden id="qlinsert_content_<?= $k ?>"><?= $e($insert->content) ?></textarea>
                    <button type="button" class="btn btn-secondary button-well" data-qlinsert="qlinsert_content_<?= $k ?>">
                        <?php if ($insert->icon !== ''): ?><i class="fa <?= $e($insert->icon) ?>" aria-hidden="true"></i> <?php endif; ?>
                        <?= $e($insert->label) ?>
                    </button>
                    <?= $e(mb_substr(strip_tags($insert->content), 0, 25)) ?>...
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
