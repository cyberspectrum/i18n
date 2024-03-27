<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Job;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\DictionaryBuilder\DictionaryBuilderInterface;
use CyberSpectrum\I18N\JobBuilder\JobBuilderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use UnexpectedValueException;

/**
 * This builds translation jobs.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class JobFactory
{
    /** The dictionary builders. */
    private ServiceLocator $dictionaryBuilders;

    /** The job type builders. */
    private ServiceLocator $jobBuilders;

    /** The configuration. */
    private Configuration $configuration;

    /** The logger. */
    private LoggerInterface $logger;

    /**
     * Create a new instance.
     *
     * @param ServiceLocator  $dictionaryBuilders The dictionary builders.
     * @param ServiceLocator  $jobBuilders        The job type builders.
     * @param Configuration   $configuration      The configuration to process.
     * @param LoggerInterface $logger             The logger.
     */
    public function __construct(
        ServiceLocator $dictionaryBuilders,
        ServiceLocator $jobBuilders,
        Configuration $configuration,
        LoggerInterface $logger
    ) {
        $this->dictionaryBuilders = $dictionaryBuilders;
        $this->jobBuilders        = $jobBuilders;
        $this->configuration      = $configuration;
        $this->logger             = $logger;
    }

    /**
     * Obtain the job names.
     *
     * @return list<string>
     */
    public function getJobNames(): array
    {
        return $this->configuration->getJobNames();
    }

    /**
     * Process a configuration.
     *
     * @param string $name The name of the job to get.
     *
     * @throws UnexpectedValueException When the requested job is not configured.
     */
    public function createJobByName(string $name): TranslationJobInterface
    {
        if (!$this->configuration->hasJob($name)) {
            throw new UnexpectedValueException('Job "' . $name . '" not found in configuration');
        }

        return $this->createJob($this->configuration->getJob($name));
    }

    /**
     * Process a configuration.
     *
     * @param Definition $configuration The job definition.
     *
     * @throws UnexpectedValueException When the requested job is not configured.
     */
    public function createJob(Definition $configuration): TranslationJobInterface
    {
        $type = $configuration->get('type');
        if (!is_string($type) || !$this->jobBuilders->has($type)) {
            throw new UnexpectedValueException('Unknown job type ' . var_export($type, true));
        }

        $builder = $this->jobBuilders->get($type);
        if (!$builder instanceof JobBuilderInterface) {
            throw new RuntimeException('Invalid job builder registered for type ' . $type);
        }

        $job = $builder->build($this, $configuration);
        if ($job instanceof LoggerAwareInterface) {
            $job->setLogger($this->logger);
        }

        return $job;
    }

    /**
     * Create a dictionary.
     *
     * @param DictionaryDefinition $definition The dictionary configuration.
     */
    public function createDictionary(DictionaryDefinition $definition): DictionaryInterface
    {
        $builder = $this->getDictionaryBuilder($definition);

        return $builder->build($this, $definition);
    }

    /**
     * Create a dictionary.
     *
     * @param DictionaryDefinition $definition The dictionary configuration.
     */
    public function createWritableDictionary(DictionaryDefinition $definition): WritableDictionaryInterface
    {
        $builder = $this->getDictionaryBuilder($definition);

        return $builder->buildWritable($this, $definition);
    }

    /**
     * Get a dictionary builder for the passed definition.
     *
     * @param DictionaryDefinition $definition The definition.
     */
    private function getDictionaryBuilder(DictionaryDefinition $definition): DictionaryBuilderInterface
    {
        if (!$this->dictionaryBuilders->has($typeName = $definition->getType())) {
            $typeName = 'default';
        }
        $builder = $this->dictionaryBuilders->get($typeName);
        if (!$builder instanceof DictionaryBuilderInterface) {
            throw new RuntimeException('Invalid dictionary builder registered for type ' . $typeName);
        }

        return $builder;
    }
}
