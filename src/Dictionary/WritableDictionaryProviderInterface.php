<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Dictionary;

use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use InvalidArgumentException;
use Traversable;

/**
 * This interface describes a dictionary provider for writable dictionaries.
 */
interface WritableDictionaryProviderInterface
{
    /**
     * Obtain the list of available dictionary names.
     *
     * @return Traversable<int, DictionaryInformation>
     */
    public function getAvailableWritableDictionaries(): Traversable;

    /**
     * Obtain a dictionary by name.
     *
     * @param string               $name           The dictionary name.
     * @param string               $sourceLanguage The source language.
     * @param string               $targetLanguage The target language.
     * @param array<string, mixed> $customData     Custom data for initialization -
     *                                             structure is subject to the implementation.
     *
     * @return WritableDictionaryInterface
     *
     * @throws DictionaryNotFoundException When the dictionary has not been created.
     */
    public function getDictionaryForWrite(
        string $name,
        string $sourceLanguage,
        string $targetLanguage,
        array $customData = []
    ): WritableDictionaryInterface;

    /**
     * Create a dictionary with the given name.
     *
     * @param string               $name           The dictionary name.
     * @param string               $sourceLanguage The source language.
     * @param string               $targetLanguage The target language.
     * @param array<string, mixed> $customData     Custom data for initialization -
     *                                             structure is subject to the implementation.
     *
     * @return WritableDictionaryInterface
     *
     * @throws InvalidArgumentException When the dictionary has already been created.
     */
    public function createDictionary(
        string $name,
        string $sourceLanguage,
        string $targetLanguage,
        array $customData = []
    ): WritableDictionaryInterface;
}
