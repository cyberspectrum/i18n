<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration;

use Exception;

/**
 * LoaderInterface is the interface implemented by all loader classes.
 */
interface LoaderInterface
{
    /**
     * Loads a resource.
     *
     * @param mixed       $resource The resource.
     * @param string|null $type     The resource type or null if unknown.
     *
     * @throws Exception If something went wrong.
     */
    public function load($resource, ?string $type = null): void;

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource.
     * @param string|null $type     The resource type or null if unknown.
     *
     * @return bool True if this class supports the given resource, false otherwise.
     */
    public function supports($resource, ?string $type = null): bool;
}
