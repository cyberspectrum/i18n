<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\Definition;

/**
 * This defines a decorated dictionary.
 *
 * @psalm-import-type TDictionaryDefinitionConfigurationArray from DictionaryDefinition
 */
class DecoratedDictionaryDefinition extends DictionaryDefinition
{
    /** @use ExtendedDefinitionTrait<TDictionaryDefinitionConfigurationArray> */
    use ExtendedDefinitionTrait;

    /** The delegated definition. */
    private DictionaryDefinition $delegated;

    /**
     * Create a new instance.
     *
     * @param DictionaryDefinition                    $definition The definition to decorate.
     * @param TDictionaryDefinitionConfigurationArray $overrides  The values to decorate with.
     */
    public function __construct(DictionaryDefinition $definition, array $overrides)
    {
        parent::__construct($definition->getName(), $overrides);
        $this->delegated = $definition;
    }

    protected function getDelegated(): Definition
    {
        return $this->delegated;
    }
}
