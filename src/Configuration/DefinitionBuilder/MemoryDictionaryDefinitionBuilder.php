<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use InvalidArgumentException;

/**
 * Builds memory dictionary definitions.
 *
 * @psalm-type TMemoryDictionaryConfigurationArray=array{
 *   name: string,
 * }
 */
class MemoryDictionaryDefinitionBuilder implements DefinitionBuilderInterface
{
    public function build(Configuration $configuration, array $data): Definition
    {
        $this->checkConfiguration($data);
        $name = $data['name'];
        unset($data['name']);
        $data['type'] = 'memory';

        return new DictionaryDefinition($name, $data);
    }

    /** @psalm-assert TMemoryDictionaryConfigurationArray $data */
    private function checkConfiguration(array $data): void
    {
        if (!array_key_exists('name', $data)) {
            throw new InvalidArgumentException('Missing key \'name\'');
        }
        if (!is_string($data['name'])) {
            throw new InvalidArgumentException('\'name\' must be a string');
        }
    }
}
