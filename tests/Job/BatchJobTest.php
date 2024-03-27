<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Job;

use CyberSpectrum\I18N\Job\BatchJob;
use CyberSpectrum\I18N\Job\TranslationJobInterface;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Job\BatchJob */
class BatchJobTest extends TestCase
{
    public function testDryRun(): void
    {
        $batch = new BatchJob([
            'child1' => $child1 = $this->getMockForAbstractClass(TranslationJobInterface::class),
            'child2' => $child2 = $this->getMockForAbstractClass(TranslationJobInterface::class),
        ]);

        $child1->expects($this->once())->method('run')->with(true);
        $child2->expects($this->once())->method('run')->with(true);

        $batch->run(true);
    }

    public function testNoDryRun(): void
    {
        $batch = new BatchJob([
            'child1' => $child1 = $this->getMockForAbstractClass(TranslationJobInterface::class),
            'child2' => $child2 = $this->getMockForAbstractClass(TranslationJobInterface::class),
        ]);

        $child1->expects($this->once())->method('run')->with(false);
        $child2->expects($this->once())->method('run')->with(false);

        $batch->run(false);
    }

    public function testDefaultRun(): void
    {
        $batch = new BatchJob([
            'child1' => $child1 = $this->getMockForAbstractClass(TranslationJobInterface::class),
            'child2' => $child2 = $this->getMockForAbstractClass(TranslationJobInterface::class),
        ]);

        $child1->expects($this->once())->method('run')->with(null);
        $child2->expects($this->once())->method('run')->with(null);

        $batch->run();
    }
}
