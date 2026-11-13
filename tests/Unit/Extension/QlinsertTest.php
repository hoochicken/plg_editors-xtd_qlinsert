<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Tests\Unit\Extension;

use Hoochicken\Plugin\EditorsXtd\Qlinsert\Extension\Qlinsert;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Editor\Button\Button;
use Joomla\CMS\Editor\Button\ButtonsRegistry;
use Joomla\CMS\Event\Editor\EditorButtonsSetupEvent;
use Joomla\CMS\Event\Plugin\AjaxEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Session\Session;
use Joomla\CMS\User\User;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\CMS\WebAsset\WebAssetRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Qlinsert::class)]
final class QlinsertTest extends TestCase
{
    private const SECRET        = 'test-secret';
    private const SESSION_TOKEN = 'session-token';

    private CMSApplication&MockObject $app;
    private User $user;
    private bool $sessionIsNew = false;

    protected function setUp(): void
    {
        $this->user        = new User();
        $this->user->id    = 42;
        $this->user->guest = 0;

        $language = $this->createMock(Language::class);
        $language->method('_')->willReturnArgument(0);
        $language->method('getPaths')->willReturn(['loaded' => true]);

        $session = $this->createMock(Session::class);
        $session->method('get')->willReturnCallback(fn (string $key) => $key === 'user' ? $this->user : null);
        $session->method('getToken')->willReturn(self::SESSION_TOKEN);
        $session->method('isNew')->willReturnCallback(fn () => $this->sessionIsNew);

        $this->app = $this->createMock(CMSApplication::class);
        $this->app->method('getLanguage')->willReturn($language);
        $this->app->method('getSession')->willReturn($session);
        $this->app->method('getIdentity')->willReturnCallback(fn () => $this->user);
        $this->app->method('get')->willReturnCallback(fn (string $key, $default = null) => $key === 'secret' ? self::SECRET : $default);
        $this->app->method('getInput')->willReturnCallback(fn () => new \Joomla\Input\Input());

        Factory::$application = $this->app;
        Factory::$language    = $language;

        $_GET = [];
    }

    protected function tearDown(): void
    {
        Factory::$application = null;
        Factory::$language    = null;
        $_GET                 = [];
    }

    // ------------------------------------------------------------------ helpers

    private function createPlugin(array $params = []): Qlinsert
    {
        $plugin = new Qlinsert(['name' => 'qlinsert', 'type' => 'editors-xtd', 'params' => $params]);
        $plugin->setApplication($this->app);

        return $plugin;
    }

    /** @return Button[] */
    private function setupButtons(Qlinsert $plugin, array $disabled = []): array
    {
        $registry = new ButtonsRegistry();
        $event    = new EditorButtonsSetupEvent('onEditorButtonsSetup', [
            'subject'         => $registry,
            'editorType'      => 'tinymce',
            'disabledButtons' => $disabled,
        ]);

        $plugin->onEditorButtonsSetup($event);

        return $registry->getAll();
    }

    private function validFormToken(): string
    {
        return md5(self::SECRET . $this->user->id . self::SESSION_TOKEN);
    }

    private function sendValidToken(): void
    {
        $_GET[$this->validFormToken()] = '1';
    }

    private function expectAssetsLoaded(int $times = 1): void
    {
        $registry = $this->createMock(WebAssetRegistry::class);
        $registry->expects($this->exactly($times))
            ->method('addExtensionRegistryFile')
            ->with('plg_editors-xtd_qlinsert')
            ->willReturnSelf();

        $wa = $this->createMock(WebAssetManager::class);
        $wa->method('getRegistry')->willReturn($registry);
        $wa->method('__call')->willReturnSelf();

        $document = $this->createMock(Document::class);
        $document->method('getWebAssetManager')->willReturn($wa);

        $this->app->method('getDocument')->willReturn($document);
    }

    private function runAjax(Qlinsert $plugin): string
    {
        $event = new AjaxEvent('onAjaxQlinsert', ['subject' => $this->app]);
        $plugin->onAjaxQlinsert($event);

        $result = $event->getArgument('result');
        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        return $result[0];
    }

    // -------------------------------------------------------------- subscriber

    public function testSubscribesToEditorAndAjaxEvents(): void
    {
        $this->assertSame(
            ['onEditorButtonsSetup' => 'onEditorButtonsSetup', 'onAjaxQlinsert' => 'onAjaxQlinsert'],
            Qlinsert::getSubscribedEvents()
        );

        foreach (Qlinsert::getSubscribedEvents() as $method) {
            $this->assertTrue(method_exists(Qlinsert::class, $method), "Handler $method missing");
        }
    }

    public function testFieldsListDefaultAndTwelveInserts(): void
    {
        $this->assertSame('default', Qlinsert::FIELDS[0]);
        $this->assertCount(13, Qlinsert::FIELDS);
        $this->assertSame('insert12', Qlinsert::FIELDS[12]);
    }

    // ---------------------------------------------------------- editor button

    public function testNoButtonWhenDisabledForEditor(): void
    {
        $this->assertSame([], $this->setupButtons($this->createPlugin(), ['qlinsert']));
    }

    public function testOtherDisabledButtonsDoNotAffectQlinsert(): void
    {
        $this->assertCount(1, $this->setupButtons($this->createPlugin(), ['image', 'readmore']));
    }

    public function testModalButtonByDefault(): void
    {
        $buttons = $this->setupButtons($this->createPlugin());

        $this->assertCount(1, $buttons);
        $button = $buttons[0];

        $this->assertInstanceOf(Button::class, $button);
        $this->assertSame('qlinsert', $button->getButtonName());
        $this->assertSame('modal', $button->get('action'));
        $this->assertSame('PLG_EDITORS-XTD_QLINSERT_BUTTON', $button->get('text'));
        $this->assertSame('editors-xtd_qlinsert', $button->get('name'));
        $this->assertSame('edit', $button->get('icon'));
        $this->assertStringStartsWith('<svg', $button->get('iconSVG'));
    }

    public function testModalLinkPointsToComAjaxWithFormToken(): void
    {
        $link = $this->setupButtons($this->createPlugin())[0]->get('link');

        $this->assertStringStartsWith('index.php?option=com_ajax&plugin=qlinsert&group=editors-xtd&format=html&tmpl=component&', $link);
        $this->assertStringEndsWith('&' . $this->validFormToken() . '=1', $link);
    }

    public function testOneclickButtonInsertsLoremIpsumWhenNoDefaultSet(): void
    {
        $button = $this->setupButtons($this->createPlugin(['oneclick' => 1]))[0];

        $this->assertSame('insert', $button->get('action'));
        $this->assertNull($button->get('link'));
        $this->assertStringStartsWith('<p>Lorem ipsum', $button->getOptions()['content']);
    }

    #[DataProvider('defaultContentProvider')]
    public function testOneclickWrapsDefaultLinesInParagraphs(string $default, string $expected): void
    {
        $button = $this->setupButtons($this->createPlugin(['oneclick' => 1, 'default' => $default]))[0];

        $this->assertSame($expected, $button->getOptions()['content']);
    }

    public static function defaultContentProvider(): array
    {
        return [
            'single line'      => ['{loadmodule 1}', '<p>{loadmodule 1}</p>'],
            'unix newlines'    => ["one\ntwo", '<p>one</p><p>two</p>'],
            'windows newlines' => ["one\r\ntwo\r\nthree", '<p>one</p><p>two</p><p>three</p>'],
        ];
    }

    public function testOneclickOffWhenParamIsZero(): void
    {
        $button = $this->setupButtons($this->createPlugin(['oneclick' => 0, 'default' => 'x']))[0];

        $this->assertSame('modal', $button->get('action'));
    }

    // ------------------------------------------------------------- ajax: access

    public function testAjaxRejectsNonWebApplication(): void
    {
        $plugin = $this->createPlugin();
        $plugin->setApplication($this->createMock(CMSApplicationInterface::class));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(403);

        $plugin->onAjaxQlinsert(new AjaxEvent('onAjaxQlinsert', ['subject' => $this->app]));
    }

    public function testAjaxRejectsMissingToken(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('JERROR_ALERTNOAUTHOR');

        $this->runAjax($this->createPlugin(['insert1' => 'x']));
    }

    public function testAjaxRejectsWrongToken(): void
    {
        $_GET['0123456789abcdef0123456789abcdef'] = '1';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(403);

        $this->runAjax($this->createPlugin(['insert1' => 'x']));
    }

    public function testAjaxRejectsGuestEvenWithValidToken(): void
    {
        $this->sendValidToken();
        $this->user->guest = 1;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(403);

        $this->runAjax($this->createPlugin(['insert1' => 'x']));
    }

    // ------------------------------------------------------------ ajax: output

    public function testAjaxShowsHintWhenNothingConfigured(): void
    {
        $this->sendValidToken();
        $this->expectAssetsLoaded();

        $html = $this->runAjax($this->createPlugin(['insert1' => "   \n  "]));

        $this->assertStringContainsString('PLG_EDITORS-XTD_QLINSERT_MSG_NOVALUESDEFINED', $html);
        $this->assertStringNotContainsString('<ul', $html);
    }

    public function testAjaxRendersOneButtonPerFilledField(): void
    {
        $this->sendValidToken();
        $this->expectAssetsLoaded();

        $html = $this->runAjax($this->createPlugin([
            'default'      => 'Default text',
            'labelDefault' => 'Default label',
            'insert1'      => '',
            'insert3'      => '<p>Third</p>',
            'labelInsert3' => 'Third label',
            'insert3icon'  => 'fa-anchor',
            'insert12'     => 'Twelfth',
        ]));

        $this->assertSame(3, substr_count($html, 'data-qlinsert='));
        // Indices are sequential over the rendered inserts, not the param names
        $this->assertStringContainsString('id="qlinsert_content_0"', $html);
        $this->assertStringContainsString('id="qlinsert_content_2"', $html);
        $this->assertStringNotContainsString('qlinsert_content_3', $html);

        $this->assertStringContainsString('Default label', $html);
        $this->assertStringContainsString('Third label', $html);
        $this->assertStringContainsString('<i class="fa fa-anchor" aria-hidden="true"></i>', $html);

        // Order follows FIELDS
        $this->assertLessThan(strpos($html, 'Third label'), strpos($html, 'Default label'));
        $this->assertLessThan(strpos($html, 'Twelfth'), strpos($html, 'Third label'));
    }

    public function testAjaxUsesDashWhenLabelMissingAndNoIconWhenEmpty(): void
    {
        $this->sendValidToken();
        $this->expectAssetsLoaded();

        $html = $this->runAjax($this->createPlugin(['insert5' => 'content', 'insert5icon' => '  ']));

        $this->assertMatchesRegularExpression('#<button[^>]*>\s*-\s*</button>#', $html);
        $this->assertStringNotContainsString('<i class="fa', $html);
    }

    public function testAjaxTrimsContent(): void
    {
        $this->sendValidToken();
        $this->expectAssetsLoaded();

        $html = $this->runAjax($this->createPlugin(['insert1' => "  \n<p>x</p>\n  "]));

        $this->assertStringContainsString('<textarea hidden id="qlinsert_content_0">&lt;p&gt;x&lt;/p&gt;</textarea>', $html);
    }

    public function testAjaxEscapesContentLabelAndIcon(): void
    {
        $this->sendValidToken();
        $this->expectAssetsLoaded();

        $html = $this->runAjax($this->createPlugin([
            'insert1'      => '</textarea><script>alert(1)</script>',
            'labelInsert1' => '<b onmouseover="x">Label</b>',
            'insert1icon'  => 'fa-x" onclick="evil()',
        ]));

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('</textarea><script', $html);
        $this->assertStringNotContainsString('<b onmouseover', $html);
        $this->assertStringNotContainsString('onclick="evil()"', $html);
        $this->assertStringContainsString('&lt;/textarea&gt;&lt;script&gt;', $html);
    }

    public function testAjaxPreviewIsStrippedAndShortened(): void
    {
        $this->sendValidToken();
        $this->expectAssetsLoaded();

        $html = $this->runAjax($this->createPlugin(['insert1' => '<p>' . str_repeat('ä', 40) . '</p>']));

        // Preview text follows the button; the hidden textarea keeps the full content
        $preview = trim(substr($html, strpos($html, '</button>') + \strlen('</button>')));

        $this->assertStringStartsWith(str_repeat('ä', 25) . '...', $preview);
        $this->assertStringNotContainsString(str_repeat('ä', 26), $preview);
        $this->assertStringNotContainsString('&lt;p&gt;', $preview);
    }

    public function testAjaxAcceptsTokenFromCsrfHeader(): void
    {
        $_SERVER['HTTP_X_CSRF_TOKEN'] = $this->validFormToken();
        $this->expectAssetsLoaded();

        try {
            $html = $this->runAjax($this->createPlugin(['insert1' => 'x']));
        } finally {
            unset($_SERVER['HTTP_X_CSRF_TOKEN']);
        }

        $this->assertStringContainsString('qlinsert_content_0', $html);
    }
}
