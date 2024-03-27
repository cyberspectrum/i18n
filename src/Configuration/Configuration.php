<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration;

use CyberSpectrum\I18N\Configuration\Definition\Definition;
use InvalidArgumentException;

/**
 * This stores a config.
 */
class Configuration
{
    /**
     * The list of configured jobs.
     *
     * @var array<string, Definition>
     */
    private array $jobs = [];

    /**
     * The list of dictionary configurations.
     *
     * @var array<string, Definition>
     */
    private array $dictionaries = [];

    /**
     * Check if the job exists.
     *
     * @param string $name The name of the job.
     */
    public function hasJob(string $name): bool
    {
        return array_key_exists($name, $this->jobs);
    }

    /**
     * Get the configuration array for a job.
     *
     * @param string $name The name of the job.
     *
     * @throws InvalidArgumentException When the job does not exist.
     */
    public function getJob(string $name): Definition
    {
        if (!$this->hasJob($name)) {
            throw new InvalidArgumentException('Job not found: ' . $name);
        }

        return $this->jobs[$name];
    }

    /**
     * Update the definition of a job.
     *
     * @param Definition $definition The definition to set.
     */
    public function setJob(Definition $definition): void
    {
        $this->jobs[$definition->getName()] = $definition;
    }

    /**
     * Obtain the job names.
     *
     * @return list<string>
     */
    public function getJobNames(): array
    {
        return array_keys($this->jobs);
    }

    /**
     * Check if a dictionary is defined.
     *
     * @param string $name The name of the dictionary.
     */
    public function hasDictionary(string $name): bool
    {
        return array_key_exists($name, $this->dictionaries);
    }

    /**
     * Get the configuration for a dictionary.
     *
     * @param string $name The dictionary name.
     *
     * @throws InvalidArgumentException When the dictionary is not found.
     */
    public function getDictionary(string $name): Definition
    {
        if (!$this->hasDictionary($name)) {
            throw new InvalidArgumentException('Dictionary not found: ' . $name);
        }

        return $this->dictionaries[$name];
    }

    /**
     * Update the definition of a dictionary.
     *
     * @param Definition $definition The definition to set.
     */
    public function setDictionary(Definition $definition): void
    {
        $this->dictionaries[$definition->getName()] = $definition;
    }

    /**
     * Obtain the job names.
     *
     * @return list<string>
     */
    public function getDictionaryNames(): array
    {
        return array_keys($this->dictionaries);
    }
}
