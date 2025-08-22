<?php
/**
 * @package        mod_qlform
 * @copyright    Copyright (C) 2025 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
jimport('joomla.html.html');
//import the necessary class definition for formfield
jimport('joomla.form.formfield');

class JFormFieldFa extends JFormField
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
        $this->value = trim($this->value);
        $faClassesRaw = $this->getFaClassesByFile($this->filepath);
        $faClasses = [];
        $insertCounter = substr(substr($this->name, strrpos($this->name, '[') + 1), 0, -1);
        foreach ($faClassesRaw as $faSelector) {
            $faClasses[] = array_values(array_filter(explode(' ', $faSelector)));
        }
        $script = sprintf('javascript:document.getElementById(\'display_%s\').className = \'\'; document.getElementById(\'display_%s\').className = \'fa \' + this.value;', $insertCounter, $insertCounter, trim($this->value));
        $html = '';
        $html .= sprintf('<i id="display_%s" class="fa %s"></i>&nbsp;&nbsp;&nbsp;', $insertCounter, trim($this->value));
        $html .= sprintf('<select onchange="%s" class="form-control" style="display:inline-block;width:95%%;font-family: \'Font Awesome\ 5 Free\', Sans-serif;" name="%s" id="%s">', $script, $this->name, $this->id);
        $html .= sprintf('<option %s value="">%s</option>', empty(trim($this->value)) ? 'selected' : '', Text::_('PLG_EDITORS-XTD_QLINSERT_CHOOSEPLZ'));
        foreach ($faClasses as $class) {
            $glyphCode =  (2 !== count($class))
                ? $class[1] = ''
                : sprintf('&#%s;', $class[1]);
            $label = $class[0];
            $labelLabel = substr($label, 3);
            $html .= sprintf('<option %s value="%s">%s %s</option>', $label === trim($this->value) ? 'selected' : '', $label, $labelLabel, $glyphCode);

        }
        $html .= '</select>';
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
