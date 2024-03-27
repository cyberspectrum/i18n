<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\Definition;

use RuntimeException;

use function array_key_exists;
use function array_merge;
use function is_array;

/**
 * This provides a way to "wrap" a parent definition and enhance/override it with own values.
 *
 * @template TDataArray as array<string, mixed>
 *
 * @psalm-require-extends Definition
 */
trait ExtendedDefinitionTrait
{
    /**
     * @throws RuntimeException When a parent value is not of type array and can therefore not be merged.
     *
     * @return TDataArray
     *
     * @psalm-suppress MoreSpecificReturnType
     */
    public function getData(): array
    {
        /** @var TDataArray $combined */
        $combined = $this->getDelegated()->getData();
        /** @var TDataArray $parentData */
        $parentData = parent::getData();
        /** @psalm-suppress MixedAssignment */
        foreach ($parentData as $key => $value) {
            if (!array_key_exists($key, $combined)) {
                $combined[$key] = $value;
                continue;
            }
            if (is_array($value)) {
                if (!is_array($combined[$key])) {
                    throw new RuntimeException('Can not merge key "' . $key . '", parent value is not an array.');
                }

                /** @noinspection SlowArrayOperationsInLoopInspection - can not optimize here */
                $combined[$key] = array_merge($combined[$key], $value);
                continue;
            }

            $combined[$key] = $value;
        }
        /** @var TDataArray $combined */

        return $combined;
    }

    public function has(string $key): bool
    {
        return parent::has($key) || $this->getDelegated()->has($key);
    }

    public function get(string $key)
    {
        if (parent::has($key)) {
            return parent::get($key);
        }
        return $this->getDelegated()->get($key);
    }

    /** Obtain the delegator. */
    abstract protected function getDelegated(): Definition;
}
