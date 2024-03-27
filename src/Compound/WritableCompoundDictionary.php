<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Compound;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;
use InvalidArgumentException;

/**
 * This is a writable compound dictionary.
 */
class WritableCompoundDictionary extends CompoundDictionary implements WritableDictionaryInterface
{
    public function add(string $key): WritableTranslationValueInterface
    {
        [$dictionary, $remainder, $prefix] = $this->splitDictionaryRemainderAndPrefix($key);
        assert($dictionary instanceof WritableDictionaryInterface);

        return new WritableTranslationValue($prefix, $dictionary->add($remainder));
    }

    public function remove(string $key): void
    {
        [$dictionary, $remainder] = $this->splitDictionaryRemainderAndPrefix($key);
        assert($dictionary instanceof WritableDictionaryInterface);

        $dictionary->remove($remainder);
    }

    public function getWritable(string $key): WritableTranslationValueInterface
    {
        [$dictionary, $remainder, $prefix] = $this->splitDictionaryRemainderAndPrefix($key);
        assert($dictionary instanceof WritableDictionaryInterface);

        return new WritableTranslationValue($prefix, $dictionary->getWritable($remainder));
    }

    /**
     * Add a dictionary.
     *
     * @param string              $prefix     The prefix for the dictionary.
     * @param DictionaryInterface $dictionary The dictionary to add.
     *
     * @throws InvalidArgumentException When the dictionary is not writable.
     */
    public function addDictionary(string $prefix, DictionaryInterface $dictionary): void
    {
        if (!$dictionary instanceof WritableDictionaryInterface) {
            throw new InvalidArgumentException('Dictionaries in ' . __CLASS__ . ' must be writable.');
        }
        parent::addDictionary($prefix, $dictionary);
    }
}
