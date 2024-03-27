<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Job;

use CyberSpectrum\I18N\Configuration\Configuration;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This builds translation jobs.
 */
class JobFactoryFactory
{
    /** The dictionary providers. */
    private ServiceLocator $dictionaryBuilders;

    /** The job type factories. */
    private ServiceLocator $jobBuilders;

    /** The logger. */
    private LoggerInterface $logger;

    /**
     * Create a new instance.
     *
     * @param ServiceLocator  $dictionaryBuilders The dictionary builders.
     * @param ServiceLocator  $jobBuilders        The job type factories.
     * @param LoggerInterface $logger             The logger.
     */
    public function __construct(
        ServiceLocator $dictionaryBuilders,
        ServiceLocator $jobBuilders,
        LoggerInterface $logger
    ) {
        $this->dictionaryBuilders = $dictionaryBuilders;
        $this->jobBuilders        = $jobBuilders;
        $this->logger             = $logger;
    }

    /**
     * Process a configuration.
     *
     * @param Configuration $configuration The configuration to process.
     */
    public function create(Configuration $configuration): JobFactory
    {
        return new JobFactory($this->dictionaryBuilders, $this->jobBuilders, $configuration, $this->logger);
    }
}
