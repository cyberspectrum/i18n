<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\Definition;

/**
 * This describes a copy job.
 *
 * @psalm-type TCopyJobDefinitionConfigurationArray=array<string, mixed>
 * @extends Definition<TCopyJobDefinitionConfigurationArray>
 */
class CopyJobDefinition extends Definition
{
    /** The definition of the source dictionary. */
    private DictionaryDefinition $source;

    /** The definition of the target dictionary. */
    private DictionaryDefinition $target;

    /**
     * Create a new instance.
     *
     * @param string                               $name   The name of the job.
     * @param DictionaryDefinition                 $source The source dictionary.
     * @param DictionaryDefinition                 $target The target dictionary.
     * @param TCopyJobDefinitionConfigurationArray $data   The additional data.
     */
    public function __construct(
        string $name,
        DictionaryDefinition $source,
        DictionaryDefinition $target,
        array $data = []
    ) {
        parent::__construct($name, $data);
        $this->source = $source;
        $this->target = $target;
    }

    /**
     * Retrieve source.
     *
     * @return DictionaryDefinition
     */
    public function getSource(): DictionaryDefinition
    {
        return $this->source;
    }

    /**
     * Retrieve target.
     *
     * @return DictionaryDefinition
     */
    public function getTarget(): DictionaryDefinition
    {
        return $this->target;
    }
}
