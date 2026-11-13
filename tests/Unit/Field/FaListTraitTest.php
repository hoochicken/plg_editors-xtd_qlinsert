<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Tests\Unit\Field;

use Hoochicken\Plugin\EditorsXtd\Qlinsert\Field\FaListTrait;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

#[CoversTrait(FaListTrait::class)]
final class FaListTraitTest extends TestCase
{
    private function classes(): array
    {
        $subject = new class () {
            use FaListTrait {
                getFaClasses as public;
            }
        };

        return $subject->getFaClasses();
    }

    public function testReadsEveryNonEmptyLineOfDataFile(): void
    {
        $file  = \dirname(__DIR__, 3) . '/data/fa.txt';
        $lines = array_filter(array_map('trim', file($file)), static fn ($l) => $l !== '');

        $this->assertNotEmpty($lines);
        $this->assertCount(\count($lines), $this->classes());
    }

    public function testEntriesAreClassWithOptionalHexGlyph(): void
    {
        foreach ($this->classes() as $entry) {
            $this->assertMatchesRegularExpression('/^(fa|icon)-[a-z0-9-]+$/', $entry[0]);
            $this->assertLessThanOrEqual(2, \count($entry), implode(' ', $entry));

            if (isset($entry[1])) {
                $this->assertMatchesRegularExpression('/^x[0-9a-f]{4}$/i', $entry[1], $entry[0]);
            }
        }
    }

    public function testKnownEntriesAreParsed(): void
    {
        $classes = $this->classes();

        $this->assertContains(['fa-address-book', 'xf2b9'], $classes);
        $this->assertContains(['fa-accessible-icon'], $classes);
    }

    public function testClassNamesAreUnique(): void
    {
        $names = array_column($this->classes(), 0);

        $this->assertSame([], array_values(array_diff_assoc($names, array_unique($names))), 'Duplicate icons in data/fa.txt');
    }
}
