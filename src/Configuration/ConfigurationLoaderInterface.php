<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration;

use InvalidArgumentException;

/** Configuration loader interface */
interface ConfigurationLoaderInterface
{
    /**
     * Load a configuration.
     *
     * @param mixed              $source The configuration to load (file name or the like).
     * @param Configuration|null $config The configuration to load or null.
     *
     * @throws InvalidArgumentException When the configuration type is unsupported.
     */
    public function load($source, Configuration $config = null): Configuration;
}
