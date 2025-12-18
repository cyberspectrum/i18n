<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Exception;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Exception\TranslationAlreadyContainedException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TranslationAlreadyContainedException::class)]
class TranslationAlreadyContainedExceptionTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testSetsValues(): void
    {
        $previous   = new \RuntimeException();
        $dictionary = $this->getMockBuilder(DictionaryInterface::class)->getMock();
        $exception  = new TranslationAlreadyContainedException('key', $dictionary, $previous);

        self::assertSame('key', $exception->getKey());
        self::assertSame($dictionary, $exception->getDictionary());
        self::assertSame('Key "key" already contained', $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
