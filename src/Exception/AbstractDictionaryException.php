<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Exception;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use RuntimeException;
use Throwable;

/**
 * This exception is thrown by dictionaries.
 */
abstract class AbstractDictionaryException extends RuntimeException
{
    /** The dictionary. */
    private DictionaryInterface $dictionary;

    /**
     * Create a new instance.
     *
     * @param DictionaryInterface $dictionary The dictionary emitting the exception.
     * @param string              $message    The Exception message to throw.
     * @param int                 $code       The Exception code.
     * @param Throwable|null      $previous   The optional previous exception.
     */
    public function __construct(DictionaryInterface $dictionary, $message = '', $code = 0, Throwable $previous = null)
    {
        $this->dictionary = $dictionary;
        parent::__construct($message, $code, $previous);
    }

    /** Retrieve dictionary. */
    public function getDictionary(): DictionaryInterface
    {
        return $this->dictionary;
    }
}
