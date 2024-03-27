<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ExtendedDictionaryDefinition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder;
use InvalidArgumentException;

use function array_key_exists;
use function is_string;

/**
 * Builds compound dictionary definitions.
 *
 * @psalm-type TCompoundDictionaryConfiguration=string|array{
 *   name?: string,
 *   prefix?: string,
 *   type: string
 * }
 * @psalm-type TCompoundDictionaryDefinitionConfigurationArray=array{
 *   name: string,
 *   dictionaries: array<string, TCompoundDictionaryConfiguration>,
 * }
 */
class CompoundDictionaryDefinitionBuilder implements DefinitionBuilderInterface
{
    /** The definition builder to use. */
    private DefinitionBuilder $definitionBuilder;

    public function __construct(DefinitionBuilder $definitionBuilder)
    {
        $this->definitionBuilder = $definitionBuilder;
    }

    public function build(Configuration $configuration, array $data): Definition
    {
        $this->checkConfiguration($data);
        $name         = $data['name'];
        $dictionaries = [];
        foreach ($data['dictionaries'] as $prefix => $dictionary) {
            // Global defined dictionary?
            if (is_string($dictionary)) {
                $dictionaries[] = new ExtendedDictionaryDefinition($dictionary, $configuration, ['prefix' => $prefix]);
                continue;
            }

            // Create local dictionary.
            if (!isset($dictionary['name'])) {
                $dictionary['name'] = $prefix;
            }
            if (!isset($dictionary['prefix'])) {
                $dictionary['prefix'] = $prefix;
            }
            $dictionaries[] = $this->definitionBuilder->buildDictionary($configuration, $dictionary);
        }
        unset($data['name'], $data['dictionaries']);
        $data['dictionaries'] = $dictionaries;
        $data['type']         = 'compound';

        return new DictionaryDefinition($name, $data);
    }

    /** @psalm-assert TCompoundDictionaryDefinitionConfigurationArray $data */
    private function checkConfiguration(array $data): void
    {
        foreach (['name', 'dictionaries'] as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException('Missing key "' . $key . '"');
            }
        }
    }
}
