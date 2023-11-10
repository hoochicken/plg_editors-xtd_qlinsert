<?php
/**
 * @package        mod_qlform
 * @copyright    Copyright (C) 2023 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
jimport('joomla.html.html');
//import the necessary class definition for formfield
jimport('joomla.form.formfield');

class JFormFieldFadisplay extends JFormField
{
    /**
     * The form field type.
     *
     * @var  string
     * @since 1.6
     */
    protected string $filepath = __DIR__ . '/fa.txt'; //the form field type see the name is the same

    /**
     * Method to retrieve the lists that resides in your application using the API.
     *
     * @return array The field option objects.
     * @since 1.6
     */
    protected function getInput()
    {
        $faClassesRaw = $this->getFaClassesByFile($this->filepath);
        $faClasses = [];
        foreach ($faClassesRaw as $faSelector) {
            $faClasses[] = array_values(array_filter(explode(' ', $faSelector)));
        }
        $html = '<script></script>';
        $html .= '<ul class="row" style="list-style-type: none;">';
        foreach ($faClasses as $class) {
            $cssClass = $class[0] ?? '';
            $html .= sprintf('<li class="col-md-3 col-sm-4 col-xs 6"><i class="fa %s "></i> %s</li>', $cssClass, $cssClass);
        }
        $html .= '</ul>';
        return $html;
    }

    protected function getFaClassesByFile(string $filepath): array
    {
        $raw = file_exists($filepath) ? file_get_contents($filepath) : '';
        $fa = array_filter(explode("\n", (string)$raw));
        array_walk($fa, function(&$item) { $item = trim($item); });
        return $fa;
    }

}
