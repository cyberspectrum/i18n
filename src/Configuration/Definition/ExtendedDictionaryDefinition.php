<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Configuration;

/**
 * This provides a way to "wrap" a parent definition and enhance it with own values.
 *
 * @psalm-import-type TDictionaryDefinitionConfigurationArray from DictionaryDefinition
 */
class ExtendedDictionaryDefinition extends DictionaryDefinition
{
    /** @use ExtendedDefinitionTrait<TDictionaryDefinitionConfigurationArray> */
    use ExtendedDefinitionTrait;

    /** The configuration. */
    protected Configuration $configuration;

    /**
     * Create a new instance.
     *
     * @param string                                  $name          The name for the dictionary.
     * @param Configuration                           $configuration The configuration to use.
     * @param TDictionaryDefinitionConfigurationArray $data          The configuration values.
     */
    public function __construct(string $name, Configuration $configuration, array $data = [])
    {
        parent::__construct($name, $data);
        $this->configuration = $configuration;
    }

    /** Obtain the delegator. */
    protected function getDelegated(): Definition
    {
        return $this->configuration->getDictionary($this->getName());
    }
}
