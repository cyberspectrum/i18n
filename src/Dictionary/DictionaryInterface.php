<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Dictionary;

use CyberSpectrum\I18N\Exception\NotSupportedException;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;
use Traversable;

/**
 * This interface describes a dictionary.
 */
interface DictionaryInterface
{
    /**
     * Obtain all keys within the dictionary.
     *
     * @return Traversable<int, string>
     */
    public function keys(): Traversable;

    /**
     * Obtain the value for a translation key.
     *
     * @param string $key The key to obtain.
     *
     * @return TranslationValueInterface
     *
     * @throws NotSupportedException When the key is in bad or unsupported format.
     * @throws TranslationNotFoundException When the key is not found.
     */
    public function get(string $key): TranslationValueInterface;

    /**
     * Test if the key is contained.
     *
     * @param string $key The key to test.
     */
    public function has(string $key): bool;

    /** Obtain the source language */
    public function getSourceLanguage(): string;

    /** Obtain the source language */
    public function getTargetLanguage(): string;
}
