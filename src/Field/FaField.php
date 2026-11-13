<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Field;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Font Awesome icon select with live preview.
 */
class FaField extends FormField
{
    use FaListTrait;

    protected $type = 'Fa';

    protected function getInput()
    {
        $value     = trim((string) $this->value);
        $previewId = $this->id . '_preview';
        $e         = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');

        $html  = sprintf('<i id="%s" class="fa %s" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;', $e($previewId), $e($value));
        $html .= sprintf(
            '<select onchange="document.getElementById(\'%s\').className = \'fa \' + this.value;" class="form-select" '
            . 'style="display:inline-block;width:95%%;font-family:\'Font Awesome 6 Free\',\'Font Awesome 5 Free\',sans-serif;" name="%s" id="%s">',
            $e($previewId),
            $e($this->name),
            $e($this->id)
        );
        $html .= sprintf('<option %s value="">%s</option>', $value === '' ? 'selected' : '', Text::_('PLG_EDITORS-XTD_QLINSERT_CHOOSEPLZ'));

        foreach ($this->getFaClasses() as $class) {
            $label = $class[0];
            $glyph = isset($class[1]) ? sprintf('&#%s;', $e($class[1])) : '';
            $html .= sprintf(
                '<option %s value="%s">%s %s</option>',
                $label === $value ? 'selected' : '',
                $e($label),
                $e(substr($label, 3)),
                $glyph
            );
        }

        return $html . '</select>';
    }
}
