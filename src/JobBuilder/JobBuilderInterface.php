<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\JobBuilder;

use CyberSpectrum\I18N\Job\TranslationJobInterface;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Job\JobFactory;

/**
 * This describes a job builder.
 */
interface JobBuilderInterface
{
    /**
     * Build a job from the passed definition.
     *
     * @param JobFactory $factory    The job builder for recursive calls.
     * @param Definition $definition The definition.
     */
    public function build(JobFactory $factory, Definition $definition): TranslationJobInterface;
}
