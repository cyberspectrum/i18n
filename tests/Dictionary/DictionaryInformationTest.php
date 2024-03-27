<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Dictionary;

use CyberSpectrum\I18N\Dictionary\DictionaryInformation;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Dictionary\DictionaryInformation */
class DictionaryInformationTest extends TestCase
{
    public function testGetters(): void
    {
        $information = new DictionaryInformation('foo', 'en', 'de');

        self::assertSame('foo', $information->getName());
        self::assertSame('en', $information->getSourceLanguage());
        self::assertSame('de', $information->getTargetLanguage());
        self::assertSame('foo en => de', (string) $information);
    }
}
