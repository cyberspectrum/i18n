<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Memory;

use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Exception\TranslationAlreadyContainedException;
use CyberSpectrum\I18N\Exception\TranslationNotFoundException;
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;
use InvalidArgumentException;
use Traversable;

/**
 * This is a simple static memory implementation of a dictionary.
 *
 * @psalm-type TMemoryDictionaryTranslationItem = array{source?: string, target?: string}
 */
class MemoryDictionary implements WritableDictionaryInterface
{
    /**
     * The translation buffer.
     *
     * @var array<string, MemoryTranslationValue>
     */
    protected array $translationBuffer = [];

    /** The source language. */
    private string $sourceLanguage;

    /** The target language. */
    private string $targetLanguage;

    /**
     * Create a new instance.
     *
     * The translation buffer has to hold the values as:
     * [
     *   'key' => [
     *     'source' => 'Source string',
     *     'target' => 'Target string',
     *   ]
     * ]
     *
     * @param string               $sourceLanguage    The source language.
     * @param string               $targetLanguage    The target language.
     * @param array<string, array> $translationBuffer The translation buffer.
     */
    public function __construct(string $sourceLanguage, string $targetLanguage, array $translationBuffer)
    {
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguage = $targetLanguage;
        foreach ($translationBuffer as $key => $item) {
            if (array_key_exists('source', $item) && array_key_exists('target', $item)) {
                /** @var mixed $source */
                $source = $item['source'] ?? null;
                /** @var mixed $target */
                $target = $item['target'] ?? null;
                if (
                    (is_string($source) || null === $source)
                    && (is_string($target) || null === $target)
                ) {
                    $this->addItem($key, $source, $target);
                    continue;
                }
            }

            throw new InvalidArgumentException('Invalid translation array: ' . var_export($item, true));
        }
    }

    public function keys(): Traversable
    {
        foreach ($this->translationBuffer as $key => $_ignored) {
            yield $key;
        }
    }

    public function get(string $key): TranslationValueInterface
    {
        return $this->getWritable($key);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->translationBuffer);
    }

    public function getSourceLanguage(): string
    {
        return $this->sourceLanguage;
    }

    public function getTargetLanguage(): string
    {
        return $this->targetLanguage;
    }

    public function add(string $key): WritableTranslationValueInterface
    {
        if ($this->has($key)) {
            throw new TranslationAlreadyContainedException($key, $this);
        }

        return $this->addItem($key, null, null);
    }

    public function remove(string $key): void
    {
        if (!$this->has($key)) {
            throw new TranslationNotFoundException($key, $this);
        }

        unset($this->translationBuffer[$key]);
    }

    public function getWritable(string $key): WritableTranslationValueInterface
    {
        if (!$this->has($key)) {
            throw new TranslationNotFoundException($key, $this);
        }

        return $this->translationBuffer[$key];
    }

    /**
     * Add an element with the passed values.
     *
     * @param string      $key    The key to add.
     * @param string|null $source The value to use as source.
     * @param string|null $target The value to use as target.
     *
     * @throws InvalidArgumentException When the passed array is invalid.
     */
    protected function addItem(string $key, ?string $source, ?string $target): MemoryTranslationValue
    {
        return $this->translationBuffer[$key] = new MemoryTranslationValue($key, $source, $target);
    }
}
