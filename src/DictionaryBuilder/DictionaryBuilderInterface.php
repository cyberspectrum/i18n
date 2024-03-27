<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\DictionaryBuilder;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Job\JobFactory;

/**
 * This describes a dictionary builder.
 */
interface DictionaryBuilderInterface
{
    /**
     * Build a dictionary from the passed definition.
     *
     * @param JobFactory           $factory    The job builder for recursive calls.
     * @param DictionaryDefinition $definition The definition.
     */
    public function build(JobFactory $factory, DictionaryDefinition $definition): DictionaryInterface;

    /**
     * Build a writable dictionary from the passed definition.
     *
     * @param JobFactory           $factory    The job builder for recursive calls.
     * @param DictionaryDefinition $definition The definition.
     */
    public function buildWritable(JobFactory $factory, DictionaryDefinition $definition): WritableDictionaryInterface;
}
