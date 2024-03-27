<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Compound;

use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;

/**
 * This is the Compound translation value reader.
 */
class TranslationValue implements TranslationValueInterface
{
    /** The prefix. */
    protected string $prefix;

    /** The delegate. */
    protected TranslationValueInterface $delegate;

    /**
     * Create a new instance.
     *
     * @param string                    $prefix   The prefix.
     * @param TranslationValueInterface $delegate The delegate.
     */
    public function __construct(string $prefix, TranslationValueInterface $delegate)
    {
        $this->delegate = $delegate;
        $this->prefix   = $prefix;
    }

    /**
     * Obtain the translation key (this might be the same as the source value in some implementations).
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->prefix . '.' . $this->delegate->getKey();
    }

    /**
     * Obtain the source language value.
     *
     * @return string
     */
    public function getSource(): ?string
    {
        return $this->delegate->getSource();
    }

    /**
     * Obtain the target language value.
     *
     * @return string|null
     */
    public function getTarget(): ?string
    {
        return $this->delegate->getTarget();
    }

    /**
     * Check if the source value is empty.
     *
     * @return bool
     */
    public function isSourceEmpty(): bool
    {
        return $this->delegate->isSourceEmpty();
    }

    /**
     * Check if the target value is empty.
     */
    public function isTargetEmpty(): bool
    {
        return $this->delegate->isTargetEmpty();
    }
}
