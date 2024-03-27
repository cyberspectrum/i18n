<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration;

use InvalidArgumentException;

/** This loads a config file. */
abstract class AbstractConfigurationLoader implements ConfigurationLoaderInterface
{
    public function load($source, Configuration $config = null): Configuration
    {
        if (null === $config) {
            $config = new Configuration();
        }
        $loader = $this->getLoader($source, $config);
        if (!$loader->supports($source)) {
            throw new InvalidArgumentException('Unsupported configuration.');
        }

        $loader->load($source);

        return $config;
    }

    /**
     * Get a loader to populate the passed configuration.
     *
     * @param mixed         $source        The configuration source.
     * @param Configuration $configuration The configuration to populate.
     *
     * @return LoaderInterface
     */
    abstract protected function getLoader($source, Configuration $configuration): LoaderInterface;
}
