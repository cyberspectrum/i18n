<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Exception;

/**
 * This exception is thrown when a dictionary has not been found by a provider.
 */
class DictionaryNotFoundException extends \RuntimeException
{
    /** The name of the dictionary. */
    private string $name;

    /** The source language. */
    private string $sourceLanguage;

    /** The target language. */
    private string $targetLanguage;

    /**
     * Create a new instance.
     *
     * @param string $name           The name of the dictionary.
     * @param string $sourceLanguage The source language.
     * @param string $targetLanguage The target language.
     */
    public function __construct(string $name, string $sourceLanguage, string $targetLanguage)
    {
        $this->name           = $name;
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguage = $targetLanguage;
        parent::__construct(sprintf(
            'Dictionary %s not found (requested source language: "%s", requested target language: "%s").',
            $name,
            $sourceLanguage,
            $targetLanguage
        ));
    }

    /** Retrieve name. */
    public function getName(): string
    {
        return $this->name;
    }

    /** Retrieve sourceLanguage. */
    public function getSourceLanguage(): string
    {
        return $this->sourceLanguage;
    }

    /** Retrieve targetLanguage. */
    public function getTargetLanguage(): string
    {
        return $this->targetLanguage;
    }
}
