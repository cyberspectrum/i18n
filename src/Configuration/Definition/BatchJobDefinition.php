<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration\Definition;

/**
 * This describes a batch job.
 *
 * @psalm-type TBatchJobDefinitionConfigurationArray=array<string, mixed>
 * @extends Definition<TBatchJobDefinitionConfigurationArray>
 */
class BatchJobDefinition extends Definition
{
    /**
     * The job list.
     *
     * @var list<Definition>
     */
    private array $jobs;

    /**
     * @param string                                $name The name.
     * @param list<Definition>                      $jobs The job definitions.
     * @param TBatchJobDefinitionConfigurationArray $data The additional data.
     */
    public function __construct(string $name, array $jobs, array $data = [])
    {
        parent::__construct($name, $data);
        $this->jobs = $jobs;
    }

    /**
     * Retrieve jobs.
     *
     * @return list<Definition>
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }
}
