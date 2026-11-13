<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Field;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Reads data/fa.txt: one icon per line, "fa-class [glyphcode]".
 */
trait FaListTrait
{
    /**
     * @return array<int, array{0: string, 1?: string}>
     */
    protected function getFaClasses(): array
    {
        $file = \dirname(__DIR__, 2) . '/data/fa.txt';
        $raw  = is_file($file) ? (string) file_get_contents($file) : '';
        $list = [];

        foreach (preg_split('/\R/', $raw) as $line) {
            $parts = preg_split('/\s+/', trim($line), -1, PREG_SPLIT_NO_EMPTY);

            if ($parts) {
                $list[] = $parts;
            }
        }

        return $list;
    }
}
