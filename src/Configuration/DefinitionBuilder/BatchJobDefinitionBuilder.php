<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder;
use InvalidArgumentException;

/**
 * Definition builder for batch jobs.
 *
 * @psalm-type TBatchJobConfiguration=string|array{
 *   name?: string,
 *   type: string
 * }
 * @psalm-type TBatchJobDefinitionConfigurationArray=array{
 *   name: string,
 *   jobs: array<int, TBatchJobConfiguration>,
 * }
 */
class BatchJobDefinitionBuilder implements DefinitionBuilderInterface
{
    /** The definition builder to use. */
    private DefinitionBuilder $definitionBuilder;

    public function __construct(DefinitionBuilder $definitionBuilder)
    {
        $this->definitionBuilder = $definitionBuilder;
    }

    public function build(Configuration $configuration, array $data): Definition
    {
        $this->checkConfiguration($data);
        $name = $data['name'];
        $jobs = [];
        foreach ($data['jobs'] as $index => $job) {
            // Global defined job?
            if (is_string($job)) {
                $jobs[] = new ReferencedJobDefinition($job, $configuration);
                continue;
            }

            // Create local job.
            if (!isset($job['name'])) {
                $job['name'] = $name . '.' . $index;
            }
            $jobs[] = $this->definitionBuilder->buildJob($configuration, $job);
        }
        unset($data['name'], $data['jobs']);

        return new BatchJobDefinition($name, $jobs, $data);
    }

    /** @psalm-assert TBatchJobDefinitionConfigurationArray $data */
    private function checkConfiguration(array $data): void
    {
        foreach (['name', 'jobs'] as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException('Missing key "' . $key . '"');
            }
        }
    }
}
