<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Dictionary;

use function sprintf;

/**
 * This class provides information about a dictionary.
 */
class DictionaryInformation
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

    public function __toString(): string
    {
        return sprintf('%s %s => %s', $this->name, $this->sourceLanguage, $this->targetLanguage);
    }
}
