<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ExtendedDictionaryDefinition;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Configuration\Definition\ExtendedDictionaryDefinition */
class ExtendedDictionaryDefinitionTest extends TestCase
{
    /** Test that the getters are correctly evaluated. */
    public function testGettersFromTrait(): void
    {
        $configuration = new Configuration();
        $configuration->setDictionary(new DictionaryDefinition('foo', [
            'type' => 'dummy',
            'source_language' => 'en',
            'target_language' => 'de'
        ]));

        $definition = new ExtendedDictionaryDefinition('foo', $configuration, [
            'type' => 'override',
        ]);

        self::assertSame('override', $definition->getType());
        self::assertSame('en', $definition->getSourceLanguage());
        self::assertSame('de', $definition->getTargetLanguage());
    }
}
