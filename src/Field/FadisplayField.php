<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Field;

use Joomla\CMS\Form\FormField;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Overview of all available Font Awesome icons.
 */
class FadisplayField extends FormField
{
    use FaListTrait;

    protected $type = 'Fadisplay';

    protected function getInput()
    {
        $html = '<ul class="row" style="list-style-type: none;">';

        foreach ($this->getFaClasses() as $class) {
            $cssClass = htmlspecialchars($class[0], ENT_QUOTES, 'UTF-8');
            $html    .= sprintf('<li class="col-md-3 col-sm-4 col-6"><i class="fa %1$s" aria-hidden="true"></i> %1$s</li>', $cssClass);
        }

        return $html . '</ul>';
    }
}
