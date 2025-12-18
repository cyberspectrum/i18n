<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Exception;

use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DictionaryNotFoundException::class)]
class DictionaryNotFoundExceptionTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testSetsValues(): void
    {
        $exception  = new DictionaryNotFoundException('foo', 'en', 'de');

        self::assertSame('foo', $exception->getName());
        self::assertSame('en', $exception->getSourceLanguage());
        self::assertSame('de', $exception->getTargetLanguage());
        self::assertSame(
            'Dictionary foo not found (requested source language: "en", requested target language: "de").',
            $exception->getMessage()
        );
    }
}
