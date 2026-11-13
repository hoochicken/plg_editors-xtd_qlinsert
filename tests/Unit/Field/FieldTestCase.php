<?php

/**
 * @package        plg_editors-xtd_qlinsert
 * @copyright      Copyright (C) 2026 ql.de All rights reserved.
 * @author         Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Hoochicken\Plugin\EditorsXtd\Qlinsert\Tests\Unit\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Language;
use PHPUnit\Framework\TestCase;

abstract class FieldTestCase extends TestCase
{
    protected function setUp(): void
    {
        $language = $this->createMock(Language::class);
        $language->method('_')->willReturnArgument(0);

        Factory::$language = $language;
    }

    protected function tearDown(): void
    {
        Factory::$language = null;
    }

    protected function setProperties(FormField $field, array $props): void
    {
        foreach ($props as $name => $value) {
            $property = new \ReflectionProperty($field, $name);
            $property->setValue($field, $value);
        }
    }

    protected function renderInput(FormField $field): string
    {
        return (string) (new \ReflectionMethod($field, 'getInput'))->invoke($field);
    }
}
