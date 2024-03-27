<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Compound;

use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;

/**
 * This is a writable translation value.
 *
 * @property WritableTranslationValueInterface $delegate
 */
class WritableTranslationValue extends TranslationValue implements WritableTranslationValueInterface
{
    /**
     * Create a new instance.
     *
     * @param string                            $prefix   The prefix.
     * @param WritableTranslationValueInterface $delegate The delegate.
     */
    public function __construct(string $prefix, WritableTranslationValueInterface $delegate)
    {
        parent::__construct($prefix, $delegate);
    }

    public function setSource(string $value): void
    {
        $this->delegate->setSource($value);
    }

    public function setTarget(string $value): void
    {
        $this->delegate->setTarget($value);
    }

    public function clearSource(): void
    {
        $this->delegate->clearSource();
    }

    public function clearTarget(): void
    {
        $this->delegate->clearTarget();
    }
}
