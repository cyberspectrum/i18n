<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Memory;

use CyberSpectrum\I18N\Exception\TranslationAlreadyContainedException;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\Memory\MemoryDictionary;
use CyberSpectrum\I18N\Memory\MemoryTranslationValue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;
use function var_export;

/** @covers \CyberSpectrum\I18N\Memory\MemoryDictionary */
class MemoryDictionaryTest extends TestCase
{
    public function testInstantiation(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', []);

        self::assertSame('en', $dictionary->getSourceLanguage());
        self::assertSame('de', $dictionary->getTargetLanguage());
        self::assertSame([], iterator_to_array($dictionary->keys()));
    }

    public function testInstantiationWithInvalidDictionary(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid translation array: ' . var_export(['dictionary'], true));

        new MemoryDictionary('en', 'de', ['broken' => ['dictionary']]);
    }

    public function testThrowsForUnknownKey(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', []);

        $this->expectException(TranslationNotFoundException::class);
        $this->expectExceptionMessage('Key "unknown-key" not found');

        $dictionary->get('unknown-key');
    }

    public function testCreatingWithValuesWorks(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', [
            'test-key' => [
                'source' => 'Source value',
                'target' => 'Target value',
            ],
            'null-key' => [
                'source' => null,
                'target' => null,
            ],
        ]);

        self::assertSame(['test-key', 'null-key'], iterator_to_array($dictionary->keys()));

        self::assertInstanceOf(MemoryTranslationValue::class, $value = $dictionary->get('test-key'));
        self::assertSame('Source value', $value->getSource());
        self::assertSame('Target value', $value->getTarget());
        self::assertFalse($value->isSourceEmpty());
        self::assertFalse($value->isTargetEmpty());

        self::assertInstanceOf(MemoryTranslationValue::class, $value = $dictionary->get('null-key'));
        self::assertSame('null-key', $value->getKey());
        self::assertNull($value->getSource());
        self::assertNull($value->getTarget());
        self::assertTrue($value->isSourceEmpty());
        self::assertTrue($value->isTargetEmpty());
    }

    public function testAddingValuesWorks(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', []);

        self::assertInstanceOf(MemoryTranslationValue::class, $value = $dictionary->add('test-key'));
        self::assertTrue($dictionary->has('test-key'));
        self::assertSame(['test-key'], iterator_to_array($dictionary->keys()));
        self::assertSame('test-key', $value->getKey());
    }

    public function testAddingExistingValuesThrows(): void
    {
        $this->expectException(TranslationAlreadyContainedException::class);
        $this->expectExceptionMessage('Key "test-key" already contained');

        $dictionary = new MemoryDictionary('en', 'de', ['test-key' => [
            'source' => 'Source value',
            'target' => 'Target value',
        ]]);

        $dictionary->add('test-key');
    }

    public function testRemovalOfValuesWorks(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', ['test-key' => [
            'source' => 'Source value',
            'target' => 'Target value',
        ]]);

        $dictionary->remove('test-key');

        self::assertSame([], iterator_to_array($dictionary->keys()));
        self::assertFalse($dictionary->has('test-key'));
    }

    public function testRemovalOfNonExistentValueThrows(): void
    {
        $dictionary = new MemoryDictionary('en', 'de', []);

        $this->expectException(TranslationNotFoundException::class);
        $this->expectExceptionMessage('Key "unknown-key" not found');

        $dictionary->remove('unknown-key');
    }
}
