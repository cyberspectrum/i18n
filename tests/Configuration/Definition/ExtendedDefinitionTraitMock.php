<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Definition\ExtendedDefinitionTrait;
use CyberSpectrum\I18N\Configuration\Definition\Definition;

/**
 * This is a mock for the extended definition trait.
 */
class ExtendedDefinitionTraitMock extends Definition
{
    use ExtendedDefinitionTrait;

    private Definition $delegated;

    /**
     * Create a new instance.
     *
     * @param string     $name      The name.
     * @param Definition $delegated The delegator.
     * @param array      $data      The data.
     */
    public function __construct(string $name, Definition $delegated, array $data = [])
    {
        parent::__construct($name, $data);
        $this->delegated = $delegated;
    }

    protected function getDelegated(): Definition
    {
        return $this->delegated;
    }
}
