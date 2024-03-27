<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\DependencyInjection;

use Symfony\Component\DependencyInjection\ServiceLocator;

use function array_keys;

/**
 * This adds a method "ids" for being able to inspect the list of registered service ids to the service locator.
 *
 * This helps mainly for debug purposes at the expense of more memory usage.
 *
 * @psalm-suppress PropertyNotSetInConstructor - we inherit some properties that are checked via isset().
 */
class IdProvidingServiceLocator extends ServiceLocator
{
    /**
     * The id list.
     *
     * @var list<string>
     */
    private array $serviceIds;

    /** @param array<string, callable> $factories The factories. */
    public function __construct($factories)
    {
        parent::__construct($factories);
        $this->serviceIds = array_keys($factories);
    }

    /**
     * Obtain the ids of registered services.
     *
     * @return list<string>
     */
    public function ids(): array
    {
        return $this->serviceIds;
    }
}
