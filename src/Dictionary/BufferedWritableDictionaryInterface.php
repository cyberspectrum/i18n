<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Dictionary;

use RuntimeException;

/**
 * This interface describes a dictionary that can buffer updates.
 *
 * All write modifications will only be performed in memory after calling `beginBuffering()` and will only be saved by
 * calling `commitBuffer()`.
 *
 * This eases disk I/O on batch modifications but will consume more memory.
 */
interface BufferedWritableDictionaryInterface
{
    /**
     * Start buffering.
     *
     * @throws RuntimeException When the dictionary is already buffering.
     */
    public function beginBuffering(): void;

    /**
     * Persist the modifications in the buffer and end buffering.
     *
     * @throws RuntimeException When the dictionary is not currently buffering.
     */
    public function commitBuffer(): void;

    /** Check if the dictionary is currently buffering. */
    public function isBuffering(): bool;
}
