<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Job;

/**
 * This interface describes translation jobs.
 */
interface TranslationJobInterface
{
    /**
     * Run the job.
     *
     * @param bool|null $dryRun Flag if the job should run as dry run - if null, the default value in the job is used.
     */
    public function run(?bool $dryRun = null): void;
}
