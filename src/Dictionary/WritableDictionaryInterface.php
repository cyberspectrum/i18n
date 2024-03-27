<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Dictionary;

use CyberSpectrum\I18N\Exception\NotSupportedException;
use CyberSpectrum\I18N\Exception\TranslationAlreadyContainedException;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;

/**
 * This interface describes a writable dictionary.
 */
interface WritableDictionaryInterface extends DictionaryInterface
{
    /**
     * Add a value.
     *
     * @param string $key The language key to add.
     *
     * @return WritableTranslationValueInterface
     *
     * @throws TranslationAlreadyContainedException When the key is already contained and the dictionary does not allow.
     * @throws NotSupportedException When the key is in bad or unsupported format or adding is not supported.
     */
    public function add(string $key): WritableTranslationValueInterface;

    /**
     * Remove a translation value.
     *
     * @param string $key The language key to remove.
     *
     * @return void
     *
     * @throws TranslationNotFoundException When the key is not found.
     * @throws NotSupportedException When the key is in bad or unsupported format or removing is not supported.
     */
    public function remove(string $key): void;

    /**
     * Obtain a value for a translation key.
     *
     * @param string $key The key to obtain.
     *
     * @return WritableTranslationValueInterface
     *
     * @throws NotSupportedException When the key is in bad or unsupported format.
     * @throws TranslationNotFoundException When the key is not found and the dictionary does not add automatically.
     */
    public function getWritable(string $key): WritableTranslationValueInterface;
}
