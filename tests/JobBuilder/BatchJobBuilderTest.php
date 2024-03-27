<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\JobBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition;
use CyberSpectrum\I18N\Job\BatchJob;
use CyberSpectrum\I18N\Job\JobFactory;
use CyberSpectrum\I18N\Job\TranslationJobInterface;
use CyberSpectrum\I18N\JobBuilder\BatchJobBuilder;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\JobBuilder\BatchJobBuilder */
class BatchJobBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $child1 = new Definition('child1');
        $child2 = new Definition('child2');

        $builder
            ->expects($this->exactly(2))
            ->method('createJob')
            ->withConsecutive([$child1], [$child2])
            ->willReturn($this->getMockForAbstractClass(TranslationJobInterface::class));

        $definition = new BatchJobDefinition('test', [$child1, $child2]);

        $instance = new BatchJobBuilder();

        self::assertInstanceOf(BatchJob::class, $instance->build($builder, $definition));
    }

    public function testBuildUnwrapsReferencedJobs(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $child1 = new Definition('child1');
        $child2 = new Definition('child2');

        $builder
            ->expects($this->exactly(2))
            ->method('createJob')
            ->withConsecutive([$child1], [$child2])
            ->willReturn($this->getMockForAbstractClass(TranslationJobInterface::class));

        $configuration = new Configuration();
        $configuration->setJob(new BatchJobDefinition('test', [$child1, $child2]));

        $definition = new ReferencedJobDefinition('test', $configuration);
        $instance   = new BatchJobBuilder();

        self::assertInstanceOf(BatchJob::class, $instance->build($builder, $definition));
    }

    public function testBuildThrowsForInvalid(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->never())->method('createJob');

        $configuration = new Configuration();
        $configuration->setJob(new Definition('test'));

        $definition = new ReferencedJobDefinition('test', $configuration);
        $instance   = new BatchJobBuilder();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid definition passed.');

        $instance->build($builder, $definition);
    }
}
