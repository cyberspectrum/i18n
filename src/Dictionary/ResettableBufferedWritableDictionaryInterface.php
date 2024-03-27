<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Dictionary;

use RuntimeException;

/**
 * This interface describes a dictionary that can revert buffered updates.
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 */
interface ResettableBufferedWritableDictionaryInterface
{
    /**
     * Revert the modifications in the buffer and end buffering.
     *
     * @throws RuntimeException When the dictionary is not currently buffering.
     */
    public function revertBuffer(): void;
}
