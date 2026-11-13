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
 * Static info box explaining the icon options.
 */
class ExplanationField extends FormField
{
    protected $type = 'Explanation';

    protected function getInput()
    {
        return sprintf('<div class="alert alert-info">%s</div>', Text::_('PLG_EDITORS-XTD_QLINSERTICON_EXPLANATION'));
    }
}
