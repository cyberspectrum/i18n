<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Memory;

use CyberSpectrum\I18N\Dictionary\DictionaryInformation;
use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\DictionaryProviderInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryProviderInterface;
use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Traversable;

/** This provides access to the translations in the store. */
class MemoryDictionaryProvider implements
    DictionaryProviderInterface,
    WritableDictionaryProviderInterface,
    LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * The dictionaries.
     *
     * @var array<string, MemoryDictionary>
     */
    private array $dictionaries = [];

    public function getAvailableDictionaries(): Traversable
    {
        foreach ($this->dictionaries as $name => $dictionary) {
            yield $this->createInformation($name, $dictionary);
        }
    }

    public function getDictionary(
        string $name,
        string $sourceLanguage,
        string $targetLanguage,
        array $customData = []
    ): DictionaryInterface {
        if ($this->logger) {
            $this->logger->debug('Memory: opening dictionary ' . $name);
        }
        foreach ($this->dictionaries as $dictionaryName => $dictionary) {
            if (
                $dictionaryName === $name
                && $sourceLanguage === $dictionary->getSourceLanguage()
                && $targetLanguage === $dictionary->getTargetLanguage()
            ) {
                return $dictionary;
            }
        }

        throw new DictionaryNotFoundException($name, $sourceLanguage, $targetLanguage);
    }

    public function getAvailableWritableDictionaries(): Traversable
    {
        foreach ($this->dictionaries as $name => $dictionary) {
            yield $this->createInformation($name, $dictionary);
        }
    }

    public function getDictionaryForWrite(
        string $name,
        string $sourceLanguage,
        string $targetLanguage,
        array $customData = []
    ): WritableDictionaryInterface {
        if ($this->logger) {
            $this->logger->debug('Memory: opening writable dictionary ' . $name);
        }

        foreach ($this->dictionaries as $dictionaryName => $dictionary) {
            if (
                $dictionaryName === $name
                && $sourceLanguage === $dictionary->getSourceLanguage()
                && $targetLanguage === $dictionary->getTargetLanguage()
            ) {
                return $dictionary;
            }
        }

        throw new DictionaryNotFoundException($name, $sourceLanguage, $targetLanguage);
    }

    public function createDictionary(
        string $name,
        string $sourceLanguage,
        string $targetLanguage,
        array $customData = []
    ): WritableDictionaryInterface {
        if ($this->logger) {
            $this->logger->debug('Memory: creating new dictionary ' . $name);
        }

        foreach ($this->dictionaries as $dictionaryName => $dictionary) {
            if (
                $dictionaryName === $name
                && $sourceLanguage === $dictionary->getSourceLanguage()
                && $targetLanguage === $dictionary->getTargetLanguage()
            ) {
                throw new InvalidArgumentException('Dictionary ' . $name . ' already exists.');
            }
        }

        return $this->dictionaries[$name] = new MemoryDictionary($sourceLanguage, $targetLanguage, []);
    }

    /**
     * Create an information container for the passed xlf file.
     *
     * @param string           $name       The internal name.
     * @param MemoryDictionary $dictionary The dictionary.
     *
     * @return DictionaryInformation
     */
    private function createInformation(string $name, MemoryDictionary $dictionary): DictionaryInformation
    {
        return new DictionaryInformation($name, $dictionary->getSourceLanguage(), $dictionary->getTargetLanguage());
    }
}
