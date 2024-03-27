<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Exception;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Exception\TranslationNotFoundException */
class TranslationNotFoundExceptionTest extends TestCase
{
    public function testSetsValues(): void
    {
        $previous   = new \RuntimeException();
        $dictionary = $this->getMockForAbstractClass(DictionaryInterface::class);
        $exception  = new TranslationNotFoundException('key', $dictionary, $previous);

        self::assertSame('key', $exception->getKey());
        self::assertSame($dictionary, $exception->getDictionary());
        self::assertSame('Key "key" not found', $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
