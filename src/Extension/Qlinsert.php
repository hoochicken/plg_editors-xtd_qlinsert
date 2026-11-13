<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Extension;

use Joomla\CMS\Application\CMSWebApplicationInterface;
use Joomla\CMS\Editor\Button\Button;
use Joomla\CMS\Event\Editor\EditorButtonsSetupEvent;
use Joomla\CMS\Event\Plugin\AjaxEvent;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Editor button "qlinsert": inserts predefined snippets into the editor.
 */
final class Qlinsert extends CMSPlugin implements SubscriberInterface
{
    /** Param names holding insertable content, in display order. */
    public const FIELDS = [
        'default', 'insert1', 'insert2', 'insert3', 'insert4', 'insert5', 'insert6',
        'insert7', 'insert8', 'insert9', 'insert10', 'insert11', 'insert12',
    ];

    private const DEFAULT_TEXT = '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p><p>At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata.</p>';

    private const ICON_SVG = '<svg viewBox="0 0 32 32" width="24" height="24"><path d="M27 0c2.761 0 5 2.239 5 5 0 1.126-0.372 2.164-1 3l-2 2-7-7 2-2c0.836-0.628 1.874-1 3-1zM2 23l-2 9 9-2 18.5-18.5-7-7-18.5 18.5zM22.362 11.362l-14 14-1.724-1.724 14-14 1.724 1.724z"></path></svg>';

    protected $autoloadLanguage = true;

    public static function getSubscribedEvents(): array
    {
        return [
            'onEditorButtonsSetup' => 'onEditorButtonsSetup',
            'onAjaxQlinsert'       => 'onAjaxQlinsert',
        ];
    }

    /**
     * Registers the editor button.
     */
    public function onEditorButtonsSetup(EditorButtonsSetupEvent $event): void
    {
        if (\in_array($this->_name, $event->getDisabledButtons(), true)) {
            return;
        }

        $event->getButtonsRegistry()->add($this->getButton());
    }

    /**
     * Renders the modal content, called via
     * index.php?option=com_ajax&plugin=qlinsert&group=editors-xtd&format=html&tmpl=component
     */
    public function onAjaxQlinsert(AjaxEvent $event): void
    {
        $app = $this->getApplication();

        if (!$app instanceof CMSWebApplicationInterface || !Session::checkToken('get') || $app->getIdentity()->guest) {
            throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $wa = $app->getDocument()->getWebAssetManager();
        $wa->getRegistry()->addExtensionRegistryFile('plg_editors-xtd_qlinsert');
        $wa->useStyle('plg_editors-xtd_qlinsert.modal')
            ->useScript('plg_editors-xtd_qlinsert.modal');

        $inserts = [];

        foreach (self::FIELDS as $field) {
            $content = trim((string) $this->params->get($field, ''));

            if ($content === '') {
                continue;
            }

            $inserts[] = (object) [
                'content' => $content,
                'label'   => (string) $this->params->get('label' . ucfirst($field), '-'),
                'icon'    => trim((string) $this->params->get($field . 'icon', '')),
            ];
        }

        ob_start();
        include \dirname(__DIR__, 2) . '/tmpl/modal.php';
        $event->addResult(ob_get_clean());
    }

    private function getButton(): Button
    {
        $props = [
            'text'    => Text::_('PLG_EDITORS-XTD_QLINSERT_BUTTON'),
            'icon'    => 'edit',
            'iconSVG' => self::ICON_SVG,
            // Whole plugin name, needed for backward compatibility
            'name'    => $this->_type . '_' . $this->_name,
        ];

        if ((bool) $this->params->get('oneclick', 0)) {
            // Core "insert" action: replaces the selection with options.content
            $props['action'] = 'insert';

            return new Button($this->_name, $props, ['content' => $this->getDefault()]);
        }

        $props['action'] = 'modal';
        $props['link']   = 'index.php?option=com_ajax&plugin=qlinsert&group=editors-xtd&format=html&tmpl=component&'
            . Session::getFormToken() . '=1';

        // The core button layout adds popupType=iframe, header text and icon
        return new Button($this->_name, $props);
    }

    private function getDefault(): string
    {
        $default = (string) $this->params->get('default', '');

        if ($default === '') {
            return self::DEFAULT_TEXT;
        }

        $str = '<p>' . implode('</p><p>', preg_split("/\n/", $default)) . '</p>';

        return str_replace("\r", '', $str);
    }
}
