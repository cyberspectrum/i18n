<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Compound;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Exception\NotSupportedException;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;
use InvalidArgumentException;
use RuntimeException;
use Traversable;

/**
 * This is a compound dictionary.
 */
class CompoundDictionary implements DictionaryInterface
{
    /**
     * The source language.
     */
    private string $sourceLanguage;

    /**
     * The target language.
     */
    private string $targetLanguage;

    /**
     * The contained dictionaries.
     *
     * @var array<string, DictionaryInterface>
     */
    private array $dictionaries;

    /**
     * Create a new instance.
     *
     * @param string $sourceLanguage The source language
     * @param string $targetLanguage The target language.
     */
    public function __construct(string $sourceLanguage, string $targetLanguage)
    {
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguage = $targetLanguage;
        $this->dictionaries   = [];
    }

    public function keys(): Traversable
    {
        foreach ($this->dictionaries as $prefix => $dictionary) {
            foreach ($dictionary->keys() as $key) {
                yield $prefix . '.' . $key;
            }
        }
    }

    public function get(string $key): TranslationValueInterface
    {
        [$dictionary, $remainder, $prefix] = $this->splitDictionaryRemainderAndPrefix($key);

        return new TranslationValue($prefix, $dictionary->get($remainder));
    }

    public function has(string $key): bool
    {
        [$dictionary, $remainder] = $this->splitDictionaryRemainderAndPrefix($key);

        return $dictionary->has($remainder);
    }

    public function getSourceLanguage(): string
    {
        return $this->sourceLanguage;
    }

    public function getTargetLanguage(): string
    {
        return $this->targetLanguage;
    }

    /**
     * Add a dictionary.
     *
     * @param string              $prefix     The prefix for the dictionary.
     * @param DictionaryInterface $dictionary The dictionary to add.
     *
     * @throws RuntimeException When already a dictionary with the prefix is contained.
     * @throws NotSupportedException On language mismatch.
     */
    public function addDictionary(string $prefix, DictionaryInterface $dictionary): void
    {
        if (isset($this->dictionaries[$prefix])) {
            throw new RuntimeException('A dictionary with prefix "' . $prefix . '" has already been added.');
        }

        if ($this->sourceLanguage !== $real = $dictionary->getSourceLanguage()) {
            throw new NotSupportedException($this, sprintf(
                'Languages can not be mixed in compounds, expected source "%1$s" but got "%2$s"',
                $this->sourceLanguage,
                $real
            ));
        }

        if ($this->targetLanguage !== $real = $dictionary->getTargetLanguage()) {
            throw new NotSupportedException($this, sprintf(
                'Languages can not be mixed in compounds, expected target "%1$s" but got "%2$s"',
                $this->targetLanguage,
                $real
            ));
        }

        $this->dictionaries[$prefix] = $dictionary;
    }

    /**
     * Obtain the dictionary for the key.
     *
     * Returns:
     *   [DictionaryInterface, remainder, prefix]
     *
     * @param string $key The key.
     *
     * @return array{0: DictionaryInterface, 1: string, 2: string }
     */
    protected function splitDictionaryRemainderAndPrefix(string $key): array
    {
        [$remainder, $prefix] = $this->splitRemainderAndPrefix($key);

        return [$this->dictionaries[$prefix], $remainder, $prefix];
    }

    /**
     * Obtain the dictionary for the key.
     *
     * Returns:
     *   [prefix, remainder]
     *
     * @param string $key The key.
     *
     * @return array{0: string, 1: string}
     *
     * @throws InvalidArgumentException    When the key has an invalid format.
     * @throws TranslationNotFoundException When no dictionary for the key prefix is registered.
     */
    protected function splitRemainderAndPrefix(string $key): array
    {
        if (false === ($pos = strpos($key, '.'))) {
            throw new InvalidArgumentException('Key "' . $key . '" has invalid format.');
        }

        $prefix = substr($key, 0, $pos);
        if (!array_key_exists($prefix, $this->dictionaries)) {
            throw new TranslationNotFoundException($key, $this);
        }

        return [substr($key, ($pos + 1)), $prefix];
    }
}
