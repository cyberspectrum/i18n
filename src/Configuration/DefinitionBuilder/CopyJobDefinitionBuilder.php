<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ExtendedDictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\CopyJobDefinition;
use InvalidArgumentException;

use function array_key_exists;

/**
 * This is a simple key value store.
 *
 * @psalm-import-type TDictionaryDefinitionConfigurationArray from DictionaryDefinition
 * @psalm-type TCopyJobDictionaryConfigurationArray=array{name?: string}&TDictionaryDefinitionConfigurationArray
 * @psalm-type TCopyJobDictionaryConfiguration=TCopyJobDictionaryConfigurationArray|string
 * @psalm-type TCopyJobDictionaryConfigurationOverrides=array{source_language?: string, target_language?: string}
 * @psalm-type TCopyJobDefinitionConfigurationArray=array{
 *   name: string,
 *   source: TCopyJobDictionaryConfiguration,
 *   target: TCopyJobDictionaryConfiguration,
 *   source_language?: string,
 *   target_language?: string,
 * }
 */
class CopyJobDefinitionBuilder implements DefinitionBuilderInterface
{
    public function build(Configuration $configuration, array $data): Definition
    {
        $this->checkConfiguration($data);
        $overrides = $this->getDictionaryOverrides($data);
        $name      = $data['name'];
        $source    = $this->makeDictionary($configuration, $data['source'], $overrides, $name . '.source');
        $target    = $this->makeDictionary($configuration, $data['target'], $overrides, $name . '.target');
        unset($data['name'], $data['source'], $data['target']);

        return new CopyJobDefinition($name, $source, $target, $data);
    }

    /** @psalm-assert TCopyJobDefinitionConfigurationArray $data */
    private function checkConfiguration(array $data): void
    {
        foreach (['name', 'source', 'target'] as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException('Missing key "' . $key . '"');
            }
        }
    }

    /**
     * Obtain the overrides for a dictionary.
     *
     * @param TCopyJobDefinitionConfigurationArray $data The job configuration data.
     * @param-out TCopyJobDefinitionConfigurationArray $data The job configuration data.
     *
     * @return TCopyJobDictionaryConfigurationOverrides
     */
    private function getDictionaryOverrides(array &$data): array
    {
        $overrides = [];
        if (null !== $value = $data['source_language'] ?? null) {
            $overrides['source_language'] = $value;
            unset($data['source_language']);
        }
        if (null !== $value = $data['target_language'] ?? null) {
            $overrides['target_language'] = $value;
            unset($data['target_language']);
        }

        return $overrides;
    }

    /**
     * Make the passed value a valid dictionary.
     *
     * @param Configuration                            $configuration The configuration.
     * @param TCopyJobDictionaryConfiguration          $dictionary    The dictionary.
     * @param TCopyJobDictionaryConfigurationOverrides $overrides     The values to be overridden.
     * @param string                                   $path          The path for exceptions.
     *
     * @throws InvalidArgumentException When the name key is missing.
     */
    private function makeDictionary(
        Configuration $configuration,
        $dictionary,
        array $overrides,
        string $path
    ): ExtendedDictionaryDefinition {
        if (is_array($dictionary)) {
            if (!isset($dictionary['name'])) {
                throw new InvalidArgumentException('Dictionary "' . $path . '" information is missing key "name".');
            }
            $name      = $dictionary['name'];
            $overrides = array_merge($overrides, $dictionary);
            unset($overrides['name']);
            return new ExtendedDictionaryDefinition($name, $configuration, $overrides);
        }

        return new ExtendedDictionaryDefinition($dictionary, $configuration, $overrides);
    }
}
