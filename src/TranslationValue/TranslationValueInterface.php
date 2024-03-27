<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\TranslationValue;

/** This interface describes a translation value. */
interface TranslationValueInterface
{
    /** Obtain the translation key (this might be the same as the source value in some implementations). */
    public function getKey(): string;

    /** Obtain the source language value. */
    public function getSource(): ?string;

    /** Obtain the target language value. */
    public function getTarget(): ?string;

    /** Check if the source value is empty. */
    public function isSourceEmpty(): bool;

    /** Check if the target value is empty. */
    public function isTargetEmpty(): bool;
}
