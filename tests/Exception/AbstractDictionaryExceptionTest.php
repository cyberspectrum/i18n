<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Exception;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Exception\AbstractDictionaryException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/** @covers \CyberSpectrum\I18N\Exception\AbstractDictionaryException */
class AbstractDictionaryExceptionTest extends TestCase
{
    public function testSetsValues(): void
    {
        $previous   = new RuntimeException();
        $dictionary = $this->getMockForAbstractClass(DictionaryInterface::class);
        $exception  = $this->getMockForAbstractClass(
            AbstractDictionaryException::class,
            [$dictionary, 'message', 23, $previous]
        );

        self::assertSame($dictionary, $exception->getDictionary());
        self::assertSame('message', $exception->getMessage());
        self::assertSame(23, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
