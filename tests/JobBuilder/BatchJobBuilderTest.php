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
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BatchJobBuilder::class)]
class BatchJobBuilderTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testBuild(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $child1 = new Definition('child1');
        $child2 = new Definition('child2');

        $job = $this->getMockBuilder(TranslationJobInterface::class)->getMock();
        $builder
            ->expects($this->exactly(2))
            ->method('createJob')
            ->willReturnCallback(
                static function ($definition) use ($child1, $child2, $job): TranslationJobInterface {
                    static $counter = 0;
                    self::assertSame(
                        match ($counter++) {
                            0 => $child1,
                            1 => $child2,
                            default => throw new InvalidArgumentException('Invalid definition passed.')
                        },
                        $definition
                    );
                    return $job;
                }
            );

        $definition = new BatchJobDefinition('test', [$child1, $child2]);

        $instance = new BatchJobBuilder();

        self::assertInstanceOf(BatchJob::class, $instance->build($builder, $definition));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testBuildUnwrapsReferencedJobs(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $child1 = new Definition('child1');
        $child2 = new Definition('child2');

        $job = $this->getMockBuilder(TranslationJobInterface::class)->getMock();
        $builder
            ->expects($this->exactly(2))
            ->method('createJob')
            ->willReturnCallback(
                static function ($definition) use ($child1, $child2, $job): TranslationJobInterface {
                    static $counter = 0;
                    self::assertSame(
                        match ($counter++) {
                            0 => $child1,
                            1 => $child2,
                            default => throw new InvalidArgumentException('Invalid definition passed.')
                        },
                        $definition
                    );
                    return $job;
                }
            );

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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid definition passed.');

        $instance->build($builder, $definition);
    }
}
