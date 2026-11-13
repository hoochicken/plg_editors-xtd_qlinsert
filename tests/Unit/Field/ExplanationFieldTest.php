<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Tests\Unit\Field;

use Hoochicken\Plugin\EditorsXtd\Qlinsert\Field\ExplanationField;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExplanationField::class)]
final class ExplanationFieldTest extends FieldTestCase
{
    public function testRendersInfoAlertWithTranslatedText(): void
    {
        $this->assertSame(
            '<div class="alert alert-info">PLG_EDITORS-XTD_QLINSERTICON_EXPLANATION</div>',
            $this->renderInput(new ExplanationField())
        );
    }
}
