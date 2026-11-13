<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Tests\Unit\Field;

use Hoochicken\Plugin\EditorsXtd\Qlinsert\Field\FaField;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FaField::class)]
final class FaFieldTest extends FieldTestCase
{
    private function render(string $value): string
    {
        $field = new FaField();
        $this->setProperties($field, [
            'id'    => 'jform_params_insert1icon',
            'name'  => 'jform[params][insert1icon]',
            'value' => $value,
        ]);

        return $this->renderInput($field);
    }

    public function testRendersSelectWithPreview(): void
    {
        $html = $this->render('');

        $this->assertStringContainsString('<i id="jform_params_insert1icon_preview" class="fa " aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('name="jform[params][insert1icon]" id="jform_params_insert1icon"', $html);
        $this->assertStringContainsString("document.getElementById('jform_params_insert1icon_preview')", $html);
        $this->assertStringEndsWith('</select>', $html);
    }

    public function testEmptyValueSelectsPlaceholder(): void
    {
        $html = $this->render('');

        $this->assertStringContainsString('<option selected value="">PLG_EDITORS-XTD_QLINSERT_CHOOSEPLZ</option>', $html);
        $this->assertSame(1, substr_count($html, 'selected'));
    }

    public function testSelectsCurrentValueAndShowsItInPreview(): void
    {
        $html = $this->render('fa-address-book');

        $this->assertStringContainsString('class="fa fa-address-book"', $html);
        $this->assertStringContainsString('<option selected value="fa-address-book">address-book &#xf2b9;</option>', $html);
        $this->assertStringContainsString('<option  value="">', $html);
        $this->assertSame(1, substr_count($html, 'selected'));
    }

    public function testOptionWithoutGlyphHasNoEntity(): void
    {
        $this->assertStringContainsString('<option  value="fa-accessible-icon">accessible-icon </option>', $this->render(''));
    }

    public function testValueIsTrimmed(): void
    {
        $this->assertStringContainsString('<option selected value="fa-anchor">', $this->render("  fa-anchor \n"));
    }

    public function testEscapesValueInPreview(): void
    {
        $html = $this->render('"><script>x</script>');

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&quot;&gt;&lt;script&gt;', $html);
    }
}
