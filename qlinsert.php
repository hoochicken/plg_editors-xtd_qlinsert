<?php
/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2020 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Editor Qlinsert button
 *
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.article
 * @since       1.5
 */
class plgButtonQlinsert extends JPlugin
{
    private $token;

    /**
     * Constructor
     *
     * @access      protected
     * @param object $subject The object to observe
     * @param array $config An array that holds the plugin configuration
     * @since       1.5
     */
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
        $this->token = '';
        if (1 == $this->params->get('secureButSlow', 0)) $this->token = JSession::getFormToken();
    }

    /**
     * Display the button
     *
     * @return array A four element array of (article_id, article_title, category_id, object)
     */
    public function onDisplay($destination)
    {
        global $app;
        $this->destination = $destination;
        JHtml::_('behavior.modal');
        JHtml::_('bootstrap.framework');
        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($this->getJavascript());
        return $this->getButton();
    }

    private function getButton()
    {
        $button = new JObject();
        $button->set('class', 'btn');
        $button->set('text', JText::_('qlinsert'));
        $button->set('name', 'share');
        $button->set('button', 'user');

        if ('1' == $this->params->get('oneclick', '0')) {
            $button->onclick = 'insertQlinsert(\'0\',\'' . $this->token . '\');return false;';
            $button->set('link', '#');

        } else {
            $button->set('link', $this->getLink());
            $button->set('options', "{handler: 'iframe', size: {x: 520, y: 330}}");
            $button->set('modal', true);
        }
        return $button;
    }

    private function getLink()
    {
        global $app;
        JHtml::_('behavior.modal');
        $link = $app->isAdmin() ? '..' : '';
        $link .= '/plugins/editors-xtd/qlinsert/html/modal.php?';
        $link .= 'bp=' . urlencode(JURI::root());
        $link .= '&token=' . $this->token;
        return $link;
    }

    private function getJavascript()
    {
        $js = array();
        $js[] = 'var insertArray=new Array;';
        require_once(__DIR__ . '/php/classes/clsPluginEditorsxtdQlinsertHelper.php');
        $obj_qlinsertHelper = new clsPluginEditorsxtdQlinsertHelper();
        $arr = $obj_qlinsertHelper->getFieldArray();
        $i = 0;
        foreach ($arr as $k => $v) $js[] = 'insertArray[' . $i++ . ']=\'' . strip_tags(str_replace("\n", '', $this->params->get($v))) . '\';';
        $js[] = 'function insertQlinsert(integer,token)';
        $js[] = '{';
        if ('0' == $this->params->get('oneclick', '0')) $js[] = 'SqueezeBox.close();';
        if (1 == $this->params->get('secureButSlow', 0)) $js[] = 'if (token!="' . $this->token . '") { SqueezeBox.close(); return false;}';
        $js[] = 'var returnStr="";';
        $js[] = 'returnStr+=insertArray[integer];';
        $js[] = 'jInsertEditorText(returnStr, "' . $this->destination . '");';
        $js[] = '}';
        //die(implode('<br />',$js));
        return implode("\n", $js);
    }
}
