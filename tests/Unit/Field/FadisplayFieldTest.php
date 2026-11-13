<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Tests\Unit\Field;

use Hoochicken\Plugin\EditorsXtd\Qlinsert\Field\FadisplayField;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FadisplayField::class)]
final class FadisplayFieldTest extends FieldTestCase
{
    public function testListsEveryIcon(): void
    {
        $html  = $this->renderInput(new FadisplayField());
        $lines = array_filter(array_map('trim', file(\dirname(__DIR__, 3) . '/data/fa.txt')), static fn ($l) => $l !== '');

        $this->assertStringStartsWith('<ul class="row" style="list-style-type: none;">', $html);
        $this->assertStringEndsWith('</ul>', $html);
        $this->assertSame(\count($lines), substr_count($html, '<li '));
    }

    public function testRendersIconAndName(): void
    {
        $this->assertStringContainsString(
            '<li class="col-md-3 col-sm-4 col-6"><i class="fa fa-address-book" aria-hidden="true"></i> fa-address-book</li>',
            $this->renderInput(new FadisplayField())
        );
    }
}
