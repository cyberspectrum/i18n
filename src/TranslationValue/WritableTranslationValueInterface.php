<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\TranslationValue;

/**
 * This interface describes a writable translation value.
 */
interface WritableTranslationValueInterface extends TranslationValueInterface
{
    /**
     * Set the source value.
     *
     * @param string $value The new value.
     */
    public function setSource(string $value): void;

    /**
     * Set the target value.
     *
     * @param string $value The new value.
     */
    public function setTarget(string $value): void;

    /**
     * Clear the source value.
     */
    public function clearSource(): void;

    /**
     * Clear the target value.
     */
    public function clearTarget(): void;
}
