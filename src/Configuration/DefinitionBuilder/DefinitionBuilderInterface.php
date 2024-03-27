<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use InvalidArgumentException;

/**
 * This is builder can create definitions from data.
 */
interface DefinitionBuilderInterface
{
    /**
     * Build a definition from the passed values.
     *
     * @param Configuration $configuration The configuration.
     * @param array         $data          The configuration values.
     *
     * @throws InvalidArgumentException When the passed configuration is invalid.
     */
    public function build(Configuration $configuration, array $data): Definition;
}
