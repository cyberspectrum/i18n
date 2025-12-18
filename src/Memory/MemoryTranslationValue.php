<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Memory;

use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;

/**
 * This implements a simple translation value - the value is stored in a local property.
 */
final class MemoryTranslationValue implements WritableTranslationValueInterface
{
    /** The translation key. */
    private string $key;

    /** The source value. */
    private ?string $source;

    /** The target value. */
    private ?string $target;

    public function __construct(string $key, ?string $source, ?string $target)
    {
        $this->key    = $key;
        $this->source = $source;
        $this->target = $target;
    }

    #[\Override]
    public function getKey(): string
    {
        return $this->key;
    }

    #[\Override]
    public function getSource(): ?string
    {
        return $this->source;
    }

    #[\Override]
    public function getTarget(): ?string
    {
        return $this->target;
    }

    #[\Override]
    public function isSourceEmpty(): bool
    {
        $source = $this->getSource();

        return $source === null || $source === '';
    }

    #[\Override]
    public function isTargetEmpty(): bool
    {
        $target = $this->getTarget();

        return $target === null || $target === '';
    }

    #[\Override]
    public function setSource(string $value): void
    {
        $this->source = $value;
    }

    #[\Override]
    public function setTarget(string $value): void
    {
        $this->target = $value;
    }

    #[\Override]
    public function clearSource(): void
    {
        $this->source = null;
    }

    #[\Override]
    public function clearTarget(): void
    {
        $this->target = null;
    }
}
