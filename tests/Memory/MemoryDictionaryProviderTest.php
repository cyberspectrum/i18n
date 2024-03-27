<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Memory;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use CyberSpectrum\I18N\Memory\MemoryDictionary;
use CyberSpectrum\I18N\Memory\MemoryDictionaryProvider;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function array_map;
use function iterator_to_array;

/** @covers \CyberSpectrum\I18N\Memory\MemoryDictionaryProvider */
class MemoryDictionaryProviderTest extends TestCase
{
    public function testIsInitiallyEmpty(): void
    {
        $provider = new MemoryDictionaryProvider();

        self::assertSame([], iterator_to_array($provider->getAvailableDictionaries()));
        self::assertSame([], iterator_to_array($provider->getAvailableWritableDictionaries()));
    }

    public function testCanCreateDictionary(): void
    {
        $provider = new MemoryDictionaryProvider();

        $dictionary   = $provider->createDictionary('foo', 'en', 'de');
        $dictionaries = iterator_to_array($provider->getAvailableDictionaries());

        self::assertInstanceOf(DictionaryInterface::class, $dictionary);
        self::assertInstanceOf(MemoryDictionary::class, $dictionary);
        self::assertSame('en', $dictionary->getSourceLanguage());
        self::assertSame('de', $dictionary->getTargetLanguage());

        self::assertSame('foo', $dictionaries[0]->getName());
        self::assertSame('en', $dictionaries[0]->getSourceLanguage());
        self::assertSame('de', $dictionaries[0]->getTargetLanguage());
    }

    public function testCreateDictionaryThrowsForAlreadyExisting(): void
    {
        $provider = new MemoryDictionaryProvider();
        $provider->createDictionary('foo', 'en', 'de');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Dictionary foo already exists.');

        $provider->createDictionary('foo', 'en', 'de');
    }

    public function testCanGetDictionary(): void
    {
        $provider = new MemoryDictionaryProvider();
        $dictionary = $provider->createDictionary('foo', 'en', 'de');
        self::assertSame('en', $dictionary->getSourceLanguage());
        self::assertSame('de', $dictionary->getTargetLanguage());

        $dict2 = $provider->getDictionary('foo', 'en', 'de');
        self::assertInstanceOf(MemoryDictionary::class, $dict2);
        self::assertSame($dictionary, $dict2);
    }

    public function testGetDictionaryThrowsForNonExisting(): void
    {
        $provider = new MemoryDictionaryProvider();

        $this->expectException(DictionaryNotFoundException::class);
        $this->expectExceptionMessage(
            'Dictionary foo not found (requested source language: "en", requested target language: "de").'
        );

        $provider->getDictionary('foo', 'en', 'de');
    }

    public function testCanGetWritableDictionary(): void
    {
        $provider    = new MemoryDictionaryProvider();
        $dictionary1 = $provider->createDictionary('foo', 'en', 'de');
        $dictionary2 = $provider->getDictionaryForWrite('foo', 'en', 'de');
        self::assertSame('en', $dictionary1->getSourceLanguage());
        self::assertSame('de', $dictionary1->getTargetLanguage());

        self::assertInstanceOf(MemoryDictionary::class, $dictionary2);
        self::assertSame($dictionary1, $dictionary2);
    }

    public function testGetGetWritableDictionaryThrowsForNonExisting(): void
    {
        $provider = new MemoryDictionaryProvider();

        $this->expectException(DictionaryNotFoundException::class);
        $this->expectExceptionMessage(
            'Dictionary foo not found (requested source language: "en", requested target language: "de").'
        );

        $provider->getDictionaryForWrite('foo', 'en', 'de');
    }

    public function testGetAvailableMethodsReturnSameInstances(): void
    {
        $provider = new MemoryDictionaryProvider();

        $provider->createDictionary('foo1', 'en', 'de');
        $provider->createDictionary('foo2', 'en', 'de');
        $dictionaries1 = array_map('strval', iterator_to_array($provider->getAvailableDictionaries()));
        $dictionaries2 = array_map('strval', iterator_to_array($provider->getAvailableWritableDictionaries()));

        self::assertSame(['foo1 en => de', 'foo2 en => de'], $dictionaries1);
        self::assertSame(['foo1 en => de', 'foo2 en => de'], $dictionaries2);
    }
}
