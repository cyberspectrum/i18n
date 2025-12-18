<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Job;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Job\TranslationJobInterface;
use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\DictionaryBuilder\DictionaryBuilderInterface;
use CyberSpectrum\I18N\Job\JobFactory;
use CyberSpectrum\I18N\JobBuilder\JobBuilderInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use UnexpectedValueException;

#[CoversClass(JobFactory::class)]
class JobFactoryTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testGetJobNames(): void
    {
        $dictionaryBuilders = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $jobBuilders        = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $logger             = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $configuration      = new Configuration();
        $jobBuilder         = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);

        $configuration->setJob(new Definition('job', ['type' => 'test']));

        self::assertSame(['job'], $jobBuilder->getJobNames());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testThrowsForUnknownJob(): void
    {
        $dictionaryBuilders = new ServiceLocator([]);
        $jobBuilders        = new ServiceLocator([]);
        $configuration      = new Configuration();
        $logger             = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $instance = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Job "job" not found in configuration');

        $instance->createJobByName('job');
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testThrowsForUnknownJobType(): void
    {
        $dictionaryBuilders = new ServiceLocator([]);
        $jobBuilders        = new ServiceLocator([]);
        $configuration      = new Configuration();
        $logger             = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $jobDefinition      = new Definition('job', ['type' => 'test']);

        $configuration->setJob($jobDefinition);

        $instance = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown job type \'test\'');

        $instance->createJobByName('job');
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testGetJob(): void
    {
        $jobBuilder         = $this->getMockBuilder(JobBuilderInterface::class)->getMock();
        $dictionaryBuilders = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $jobBuilders        = new ServiceLocator([
            'test' => function () use ($jobBuilder) {
                return $jobBuilder;
            }
        ]);
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $configuration      = new Configuration();
        $jobDefinition      = new Definition('job', ['type' => 'test']);
        $job                = $this->getMockBuilder(TranslationJobInterface::class)->getMock();

        $configuration->setJob($jobDefinition);
        $instance = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);
        $jobBuilder->expects($this->once())->method('build')->with($instance, $jobDefinition)->willReturn($job);

        self::assertSame($job, $instance->createJobByName('job'));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testGetDictionary(): void
    {
        $dictionaryBuilder  = $this->getMockBuilder(DictionaryBuilderInterface::class)->getMock();
        $dictionaryBuilders = new ServiceLocator(['test' => function () use ($dictionaryBuilder) {
            return $dictionaryBuilder;
        }]);
        $jobBuilders        = new ServiceLocator([]);
        $configuration      = new Configuration();
        $logger             = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $dictionary         = $this->getMockBuilder(DictionaryInterface::class)->getMock();
        $definition         = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $instance = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);

        $dictionaryBuilder
            ->expects($this->once())
            ->method('build')
            ->with($instance, $definition)
            ->willReturn($dictionary);

        self::assertSame($dictionary, $instance->createDictionary($definition));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testGetDictionaryWillFallBackToDefaultOnUnknownType(): void
    {
        $dictionaryBuilder  = $this->getMockBuilder(DictionaryBuilderInterface::class)->getMock();
        $dictionaryBuilders = new ServiceLocator(['default' => function () use ($dictionaryBuilder) {
            return $dictionaryBuilder;
        }]);
        $jobBuilders        = new ServiceLocator([]);
        $configuration      = new Configuration();
        $logger             = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $dictionary         = $this->getMockBuilder(DictionaryInterface::class)->getMock();
        $definition         = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $instance = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);

        $dictionaryBuilder
            ->expects($this->once())
            ->method('build')
            ->with($instance, $definition)
            ->willReturn($dictionary);

        self::assertSame($dictionary, $instance->createDictionary($definition));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testGetDictionaryForWrite(): void
    {
        $dictionaryBuilder  = $this->getMockBuilder(DictionaryBuilderInterface::class)->getMock();
        $dictionaryBuilders = new ServiceLocator(['test' => function () use ($dictionaryBuilder) {
            return $dictionaryBuilder;
        }]);
        $jobBuilders        = new ServiceLocator([]);
        $configuration      = new Configuration();
        $logger             = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $dictionary         = $this->getMockBuilder(WritableDictionaryInterface::class)->getMock();
        $definition         = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $instance = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);

        $dictionaryBuilder
            ->expects($this->once())
            ->method('buildWritable')
            ->with($instance, $definition)
            ->willReturn($dictionary);

        self::assertSame($dictionary, $instance->createWritableDictionary($definition));
    }
}
