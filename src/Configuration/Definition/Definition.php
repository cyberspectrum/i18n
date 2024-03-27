<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\Definition;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

/**
 * This is a simple key value store.
 *
 * @template TDataArray as array<string, mixed>
 */
class Definition implements IteratorAggregate
{
    /** The name of the definition. */
    protected string $name;

    /**
     * The data.
     *
     * @var TDataArray
     */
    protected array $data;

    /**
     * @param string     $name The name.
     * @param TDataArray $data The initial data.
     */
    public function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * Retrieve name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retrieve data.
     *
     * @return TDataArray
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Check if a value has been set for the passed key.
     *
     * @param string $key The key to check.
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Obtain the value for the given key.
     *
     * @param string $key The key.
     *
     * @return mixed
     *
     * @throws InvalidArgumentException When the key is not set.
     */
    public function get(string $key)
    {
        if (!$this->has($key)) {
            throw new InvalidArgumentException('Key "' . $key . '" does not exist.');
        }
        return $this->data[$key];
    }

    /** Get an iterator over the internal array. */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getData());
    }
}
