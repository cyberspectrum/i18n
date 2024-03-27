<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Dictionary;

use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use Traversable;

/**
 * This interface describes a dictionary provider.
 */
interface DictionaryProviderInterface
{
    /**
     * Obtain the list of available dictionary names.
     *
     * @return Traversable<DictionaryInformation>
     */
    public function getAvailableDictionaries(): Traversable;

    /**
     * Obtain a dictionary by name.
     *
     * @param string               $name           The dictionary name.
     * @param string               $sourceLanguage The source language.
     * @param string               $targetLanguage The target language.
     * @param array<string, mixed> $customData     Custom data for initialization -
     *                                             structure is subject to the implementation.
     *
     * @return DictionaryInterface
     *
     * @throws DictionaryNotFoundException When the dictionary has not been created.
     */
    public function getDictionary(
        string $name,
        string $sourceLanguage,
        string $targetLanguage,
        array $customData = []
    ): DictionaryInterface;
}
