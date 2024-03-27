<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Memory;

use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;

/**
 * This implements a simple translation value - the value is stored in a local property.
 */
class MemoryTranslationValue implements WritableTranslationValueInterface
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

    public function getKey(): string
    {
        return $this->key;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function isSourceEmpty(): bool
    {
        return !$this->getSource();
    }

    public function isTargetEmpty(): bool
    {
        return !$this->getTarget();
    }

    public function setSource(string $value): void
    {
        $this->source = $value;
    }

    public function setTarget(string $value): void
    {
        $this->target = $value;
    }

    public function clearSource(): void
    {
        $this->source = null;
    }

    public function clearTarget(): void
    {
        $this->target = null;
    }
}
