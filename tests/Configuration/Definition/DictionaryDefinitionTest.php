<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/** @covers \CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition */
class DictionaryDefinitionTest extends TestCase
{
    /** Test that the getters are correctly evaluated. */
    public function testGetters(): void
    {
        $definition = new DictionaryDefinition('foo', [
            'type' => 'dummy',
            'source_language' => 'en',
            'target_language' => 'de'
        ]);

        self::assertSame('dummy', $definition->getType());
        self::assertSame('dummy', $definition->getProvider());
        self::assertSame('en', $definition->getSourceLanguage());
        self::assertSame('de', $definition->getTargetLanguage());
    }

    /** Test that the provider may be overridden. */
    public function testGetProvider(): void
    {
        $definition = new DictionaryDefinition('foo', [
            'type'            => 'dummy',
            'provider'        => 'provider',
            'source_language' => 'en',
            'target_language' => 'de'
        ]);

        self::assertSame('dummy', $definition->getType());
        self::assertSame('provider', $definition->getProvider());
        self::assertSame('en', $definition->getSourceLanguage());
        self::assertSame('de', $definition->getTargetLanguage());
    }

    public function testThrowsForMissingType(): void
    {
        $definition = new DictionaryDefinition('foo', []);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No type set for dictionary "foo"');

        $definition->getType();
    }

    public function testThrowsForMissingSourceLanguage(): void
    {
        $definition = new DictionaryDefinition('foo', []);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No source language set for dictionary "foo"');

        $definition->getSourceLanguage();
    }

    public function testThrowsForMissingTargetLanguage(): void
    {
        $definition = new DictionaryDefinition('foo', []);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No target language set for dictionary "foo"');

        $definition->getTargetLanguage();
    }
}
