<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Compound;

use CyberSpectrum\I18N\Compound\TranslationValue;
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TranslationValue::class)]
class TranslationValueTest extends TestCase
{
    public function testDelegates(): void
    {
        $value = $this->getMockBuilder(TranslationValueInterface::class)->getMock();
        $value->expects($this->once())->method('getKey')->with()->willReturn('key');
        $value->expects($this->once())->method('getSource')->with()->willReturn('source');
        $value->expects($this->once())->method('getTarget')->with()->willReturn('target');
        $value->expects($this->once())->method('isSourceEmpty')->with()->willReturn(false);
        $value->expects($this->once())->method('isTargetEmpty')->with()->willReturn(false);

        $compound = new TranslationValue('child', $value);

        self::assertInstanceOf(TranslationValue::class, $compound);
        self::assertSame('child.key', $compound->getKey());
        self::assertSame('source', $compound->getSource());
        self::assertSame('target', $compound->getTarget());
        self::assertFalse($compound->isSourceEmpty());
        self::assertFalse($compound->isTargetEmpty());
    }
}
