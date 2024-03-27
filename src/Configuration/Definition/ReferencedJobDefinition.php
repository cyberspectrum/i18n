<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Configuration;

/**
 * This provides a way to "wrap" a parent definition and enhance it with own values.
 *
 * @psalm-type TReferencedJobDefinitionConfigurationArray=array<string, mixed>
 * @extends Definition<TReferencedJobDefinitionConfigurationArray>
 */
class ReferencedJobDefinition extends Definition
{
    /** @use ExtendedDefinitionTrait<TReferencedJobDefinitionConfigurationArray> */
    use ExtendedDefinitionTrait;

    /** The configuration. */
    private Configuration $configuration;

    /**
     * Create a new instance.
     *
     * @param string                                     $name          The name for the dictionary.
     * @param Configuration                              $configuration The configuration to use.
     * @param TReferencedJobDefinitionConfigurationArray $data          The configuration values.
     */
    public function __construct(string $name, Configuration $configuration, array $data = [])
    {
        parent::__construct($name, $data);
        $this->configuration = $configuration;
    }

    public function getDelegated(): Definition
    {
        return $this->configuration->getJob($this->getName());
    }
}
