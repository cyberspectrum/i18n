<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Compound;

use CyberSpectrum\I18N\Compound\WritableTranslationValue;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Compound\WritableTranslationValue */
class WritableTranslationValueTest extends TestCase
{
    public function testDelegates(): void
    {
        $value = $this->getMockForAbstractClass(WritableTranslationValueInterface::class);
        $value->expects($this->once())->method('setSource')->with('source');
        $value->expects($this->once())->method('setTarget')->with('target');
        $value->expects($this->once())->method('clearSource');
        $value->expects($this->once())->method('clearTarget');

        $compound = new WritableTranslationValue('child', $value);

        self::assertInstanceOf(WritableTranslationValue::class, $compound);
        $compound->setSource('source');
        $compound->setTarget('target');
        $compound->clearSource();
        $compound->clearTarget();
    }
}
