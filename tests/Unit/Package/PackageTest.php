<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Consistency checks between manifest, code, language files and assets,
 * i.e. the things that break silently on install.
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Tests\Unit\Package;

use Hoochicken\Plugin\EditorsXtd\Qlinsert\Extension\Qlinsert;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class PackageTest extends TestCase
{
    private const ROOT = __DIR__ . '/../../..';

    private static function manifest(): \SimpleXMLElement
    {
        $xml = simplexml_load_file(self::ROOT . '/qlinsert.xml');
        self::assertInstanceOf(\SimpleXMLElement::class, $xml, 'qlinsert.xml is not valid XML');

        return $xml;
    }

    /** @return array<string, string> */
    private static function ini(string $file): array
    {
        $strings = parse_ini_file(self::ROOT . '/language/en-GB/' . $file, false, INI_SCANNER_RAW);
        self::assertIsArray($strings, "$file is not a valid ini file");

        return $strings;
    }

    /** @return string[] */
    private static function paramNames(): array
    {
        return array_map('strval', self::manifest()->xpath('//config/fields[@name="params"]//field/@name'));
    }

    public function testManifestBasics(): void
    {
        $xml = self::manifest();

        $this->assertSame('plugin', (string) $xml['type']);
        $this->assertSame('editors-xtd', (string) $xml['group']);
        $this->assertSame('Hoochicken\Plugin\EditorsXtd\Qlinsert', (string) $xml->namespace);
        $this->assertSame('src', (string) $xml->namespace['path']);
        $this->assertSame('qlinsert', (string) $xml->xpath('//files/folder[@plugin]')[0]['plugin']);
    }

    public function testManifestVersionMatchesAssetsAndUpdateFile(): void
    {
        $version = (string) self::manifest()->version;
        $assets  = json_decode(file_get_contents(self::ROOT . '/media/joomla.asset.json'), true, 512, JSON_THROW_ON_ERROR);
        $update  = simplexml_load_file(self::ROOT . '/_jupdate/plg_editors-xtd_qlinsert.xml');

        $this->assertSame($version, $assets['version'], 'media/joomla.asset.json version');
        $this->assertContains($version, array_map('strval', $update->xpath('//update/version')), '_jupdate has no entry for ' . $version);
    }

    public function testEveryPackagedFileExists(): void
    {
        $xml = self::manifest();

        foreach ($xml->files->children() as $node) {
            $this->assertFileExists(self::ROOT . '/' . $node);
        }

        foreach ($xml->languages->language as $node) {
            $this->assertFileExists(self::ROOT . '/language/' . $node);
        }

        foreach ($xml->media->children() as $node) {
            $this->assertFileExists(self::ROOT . '/media/' . $node);
        }
    }

    public function testEveryInsertFieldHasContentLabelAndIconParam(): void
    {
        $params = self::paramNames();

        foreach (Qlinsert::FIELDS as $field) {
            $this->assertContains($field, $params);
            $this->assertContains('label' . ucfirst($field), $params);
            $this->assertContains($field . 'icon', $params);
        }

        $this->assertContains('oneclick', $params);
    }

    public function testParamNamesAreUnique(): void
    {
        $params = self::paramNames();

        $this->assertSame([], array_values(array_diff_assoc($params, array_unique($params))));
    }

    public function testCustomFieldTypesExist(): void
    {
        $prefix = (string) self::manifest()->xpath('//config/fields[@name="params"]')[0]['addfieldprefix'];
        $core   = ['text', 'textarea', 'spacer', 'radio', 'list', 'note', 'editor', ''];

        foreach (self::manifest()->xpath('//config//field/@type') as $type) {
            $type = (string) $type;

            if (\in_array(strtolower($type), $core, true)) {
                continue;
            }

            $this->assertTrue(class_exists($prefix . '\\' . ucfirst($type) . 'Field'), "Field type '$type' has no class");
        }
    }

    public function testWebAssetsPointToShippedFiles(): void
    {
        $assets = json_decode(file_get_contents(self::ROOT . '/media/joomla.asset.json'), true, 512, JSON_THROW_ON_ERROR);
        $byType = [];

        foreach ($assets['assets'] as $asset) {
            $byType[$asset['type']][] = $asset['name'];
            $file = preg_replace('#^plg_editors-xtd_qlinsert/#', '', $asset['uri']);
            $dir  = $asset['type'] === 'style' ? 'css' : 'js';
            $this->assertFileExists(self::ROOT . "/media/$dir/$file");
        }

        // Used by Qlinsert::onAjaxQlinsert()
        $this->assertContains('plg_editors-xtd_qlinsert.modal', $byType['style'] ?? []);
        $this->assertContains('plg_editors-xtd_qlinsert.modal', $byType['script'] ?? []);
    }

    public static function iniFileProvider(): array
    {
        return [['plg_editors-xtd_qlinsert.ini'], ['plg_editors-xtd_qlinsert.sys.ini']];
    }

    #[DataProvider('iniFileProvider')]
    public function testLanguageFilesParse(string $file): void
    {
        $this->assertNotEmpty(self::ini($file));
    }

    public function testEveryLanguageKeyInCodeIsTranslated(): void
    {
        $strings = self::ini('plg_editors-xtd_qlinsert.ini');
        $files   = array_merge(
            glob(self::ROOT . '/src/*/*.php'),
            glob(self::ROOT . '/tmpl/*.php'),
            [self::ROOT . '/qlinsert.xml']
        );

        $missing = [];

        foreach ($files as $file) {
            preg_match_all('/PLG_EDITORS-XTD_QLINSERT[A-Z0-9_-]*/', file_get_contents($file), $m);

            foreach ($m[0] as $key) {
                if (!isset($strings[$key])) {
                    $missing[$key] = basename($file);
                }
            }
        }

        $this->assertSame([], $missing, 'Untranslated language keys');
    }

    public function testSysIniHasManifestStrings(): void
    {
        $strings = self::ini('plg_editors-xtd_qlinsert.sys.ini');

        $this->assertArrayHasKey((string) self::manifest()->description, $strings);
        $this->assertArrayHasKey('PLG_EDITORS-XTD_QLINSERT', $strings);
    }
}
