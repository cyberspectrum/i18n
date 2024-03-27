<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Definition\DecoratedDictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/** @covers \CyberSpectrum\I18N\Configuration\Definition\DecoratedDictionaryDefinition */
class DecoratedDictionaryDefinitionTest extends TestCase
{
    /** Test that the getters are correctly evaluated. */
    public function testGetters(): void
    {
        $definition = new DecoratedDictionaryDefinition(new DictionaryDefinition('foo', [
            'type' => 'dummy',
            'source_language' => 'en',
            'target_language' => 'de',
            'override' => false,
        ]), ['override' => true]);

        self::assertSame('dummy', $definition->getType());
        self::assertSame('en', $definition->getSourceLanguage());
        self::assertSame('de', $definition->getTargetLanguage());
        self::assertTrue($definition->has('override'));
        self::assertTrue($definition->get('override'));
    }

    public function testThrowsForMissingType(): void
    {
        $definition = new DecoratedDictionaryDefinition(new DictionaryDefinition('foo', []), []);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No type set for dictionary "foo"');

        $definition->getType();
    }

    public function testThrowsForMissingSourceLanguage(): void
    {
        $definition = new DecoratedDictionaryDefinition(new DictionaryDefinition('foo', [
            'type' => 'dummy',
            'target_language' => 'de'
        ]), []);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No source language set for dictionary "foo"');

        $definition->getSourceLanguage();
    }

    public function testThrowsForMissingTargetLanguage(): void
    {
        $definition = new DecoratedDictionaryDefinition(new DictionaryDefinition('foo', [
            'type' => 'dummy',
            'source_language' => 'en',
        ]), []);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No target language set for dictionary "foo"');

        $definition->getTargetLanguage();
    }
}
