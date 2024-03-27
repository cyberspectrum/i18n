<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\Definition;

use RuntimeException;

/**
 * This defines a dictionary.
 *
 * @psalm-type TDictionaryDefinitionConfigurationArray=array{
 *   type?: string,
 *   provider?: string,
 *   dictionary?: string,
 *   source_language?: string,
 *   target_language?: string,
 * }
 * @extends Definition<TDictionaryDefinitionConfigurationArray>
 */
class DictionaryDefinition extends Definition
{
    /**
     * Obtain the type.
     *
     * @throws RuntimeException When no type has been set.
     */
    public function getType(): string
    {
        if (!$this->has('type')) {
            throw new RuntimeException('No type set for dictionary "' . $this->getName() . '"');
        }

        /** @var string $type */
        $type = $this->get('type');

        return $type;
    }

    /**
     * Obtain the provider - falls back to the type if no special provider is set.
     *
     * @throws RuntimeException When no provider or type has been set.
     */
    public function getProvider(): string
    {
        /** @var string $provider */
        $provider = $this->has('provider') ? $this->get('provider') : $this->getType();

        return $provider;
    }

    /** Obtain the dictionary name - falls back to the name if no special dictionary is set. */
    public function getDictionary(): string
    {
        /** @var string $dictionary */
        $dictionary = $this->has('dictionary') ? $this->get('dictionary') : $this->getName();

        return $dictionary;
    }

    /**
     * Obtain the source language.
     *
     * @throws RuntimeException When no source language has been set.
     */
    public function getSourceLanguage(): string
    {
        if (!$this->has('source_language')) {
            throw new RuntimeException('No source language set for dictionary "' . $this->getName() . '"');
        }
        /** @var string $sourceLanguage */
        $sourceLanguage = $this->get('source_language');

        return $sourceLanguage;
    }

    /**
     * Obtain the target language.
     *
     * @throws RuntimeException When no target language has been set.
     */
    public function getTargetLanguage(): string
    {
        if (!$this->has('target_language')) {
            throw new RuntimeException('No target language set for dictionary "' . $this->getName() . '"');
        }
        /** @var string $targetLanguage */
        $targetLanguage = $this->get('target_language');

        return $targetLanguage;
    }
}
