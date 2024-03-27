<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Exception;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use Throwable;

/**
 * This exception is thrown when ever a translation was not found.
 */
class TranslationNotFoundException extends \RuntimeException
{
    /** The dictionary. */
    private DictionaryInterface $dictionary;

    /** The key. */
    private string $key;

    /**
     * Create a new instance.
     *
     * @param string              $key        The language key that was not found.
     * @param DictionaryInterface $dictionary The dictionary emitting the exception.
     * @param Throwable|null      $previous   The optional previous exception.
     */
    public function __construct(string $key, DictionaryInterface $dictionary, Throwable $previous = null)
    {
        $this->dictionary = $dictionary;
        $this->key        = $key;
        parent::__construct('Key "' . $key . '" not found', 0, $previous);
    }

    /** Retrieve dictionary. */
    public function getDictionary(): DictionaryInterface
    {
        return $this->dictionary;
    }

    /** Retrieve key. */
    public function getKey(): string
    {
        return $this->key;
    }
}
