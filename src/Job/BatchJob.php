<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Job;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * This job is a composite of several other jobs.
 */
class BatchJob implements TranslationJobInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * The list of translation jobs.
     *
     * @var array<string, TranslationJobInterface>
     */
    private array $jobs;

    /** @param array<string, TranslationJobInterface> $jobs */
    public function __construct(array $jobs)
    {
        $this->jobs = $jobs;
        $this->logger = null;
    }

    public function run(?bool $dryRun = null): void
    {
        foreach ($this->jobs as $name => $job) {
            if ($this->logger) {
                $this->logger->notice('Executing job: ' . $name);
            }
            $job->run($dryRun);
        }
    }
}
