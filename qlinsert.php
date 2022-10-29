<?php
/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2022 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Session\Session;
use Joomla\Event\AbstractEvent;

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
    private string $destination;
    private string $default;
    private string $token = '';

    /**
     * Constructor
     *
     * @access      protected
     * @param object $subject The object to observe
     * @param array $config An array that holds the plugin configuration
     * @since       1.5
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
        $this->setDefault();
    }

    /**
     * Display the button
     *
     * @return CMSObject element array of (article_id, article_title, category_id, object)
     */
    public function onDisplay($name)
    {
        $this->destination = $name;

        $doc = \Joomla\CMS\Factory::getDocument();
        $doc->addScript(JURI::root(true) . '/media/plg_editors-xtd_qlinsert/js/qlinsert.js');
        return $this->getButton($name);
    }

    private function getButton($name): CMSObject
    {

        $button = new CMSObject;
        $button->modal = true;
        $button->set('text', Text::_('PLG_EDITORS-XTD_qlinsert_BUTTON'));
        $button->name = $this->_type . '_' . $this->_name;
        $button->icon = 'edit';
        if ((bool)$this->params->get('oneclick', 0)) {
            $button->onclick = sprintf('window.insertQlinsert("%s", "%s");return false;', $this->destination, $this->default);
            $button->set('link', '#');
        } elseif (true) {
            $button->set('link', $this->getLink($name));
            $button->set('options', "{handler: 'iframe', size: {x: 520, y: 330}}");
            $button->set('modal', true);
        } else {
            echo '<div id="testt">TESTT</div>';
            $link = '#testt';
            $link = 'index.php?option=com_ajax&plugin=qlinsert&group=editors-xtd&method=test&format=json';
            echo $link;
            $button->set('link', $link);
            // $button->set('options', "{handler: 'iframe', size: {x: 520, y: 330}}");
            $button->set('modal', true);
        }
        return $button;
    }

    private function onAjax()
    {
        return 'Hello World';
    }

    private function onAjaxQlinsert()
    {
        return 'Hello World';
    }

    private function getLink($name)
    {
        $app = \Joomla\CMS\Factory::getApplication();
        $link = $app->isClient('administrator') ? '..' : '';
        $link .= '/plugins/editors-xtd/qlinsert/html/modal.php?';
        $link .= 'bp=' . urlencode(JURI::root());
        $link .= '&token=' . $this->token;
        $link .= '&destination=' . $name;
        return $link;
    }

    private function setDefault()
    {
        $this->default = $this->cleanString($this->params->get('default', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p><p>At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata.</p>'));
    }

    private function cleanString($str)
    {
        $arrString = preg_split("?\n?", $str);
        $str = '<p>' . implode('</p><p>', $arrString) . '</p>';
        $str = str_replace("\n", '<br />', $str);
        return str_replace("\r", '', $str);
    }
}
