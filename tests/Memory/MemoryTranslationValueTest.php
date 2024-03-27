<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Memory;

use CyberSpectrum\I18N\Memory\MemoryTranslationValue;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Memory\MemoryTranslationValue */
class MemoryTranslationValueTest extends TestCase
{
    public function testEmptyValue(): void
    {
        $value = new MemoryTranslationValue('test-key', null, null);

        self::assertSame('test-key', $value->getKey());
        self::assertNull($value->getSource());
        self::assertNull($value->getTarget());
        self::assertTrue($value->isSourceEmpty());
        self::assertTrue($value->isTargetEmpty());
    }

    public function testCreatingWithValuesWorks(): void
    {
        $value = new MemoryTranslationValue('test-key', 'Source value', 'Target value');

        self::assertSame('Source value', $value->getSource());
        self::assertSame('Target value', $value->getTarget());
        self::assertFalse($value->isSourceEmpty());
        self::assertFalse($value->isTargetEmpty());
    }

    public function testSettingValuesWorks(): void
    {
        $value = new MemoryTranslationValue('test-key', null, null);

        $value->setSource('Source value');
        $value->setTarget('Target value');

        self::assertSame('Source value', $value->getSource());
        self::assertSame('Target value', $value->getTarget());
        self::assertFalse($value->isSourceEmpty());
        self::assertFalse($value->isTargetEmpty());
    }

    public function testClearingValuesWorks(): void
    {
        $value = new MemoryTranslationValue('test-key', 'Source value', 'Target value');

        $value->clearSource();
        $value->clearTarget();

        self::assertNull($value->getSource());
        self::assertNull($value->getTarget());
        self::assertTrue($value->isSourceEmpty());
        self::assertTrue($value->isTargetEmpty());
    }
}
