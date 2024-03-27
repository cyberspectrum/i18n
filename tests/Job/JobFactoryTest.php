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
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use UnexpectedValueException;

/** @covers \CyberSpectrum\I18N\Job\JobFactory */
class JobFactoryTest extends TestCase
{
    public function testGetJobNames(): void
    {
        $dictionaryBuilders = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $jobBuilders        = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $logger             = $this->getMockForAbstractClass(LoggerInterface::class);
        $configuration      = new Configuration();
        $jobBuilder         = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);

        $configuration->setJob(new Definition('job', ['type' => 'test']));

        self::assertSame(['job'], $jobBuilder->getJobNames());
    }

    public function testThrowsForUnknownJob(): void
    {
        $dictionaryBuilders = new ServiceLocator([]);
        $jobBuilders        = new ServiceLocator([]);
        $configuration      = new Configuration();
        $logger             = $this->getMockForAbstractClass(LoggerInterface::class);

        $instance = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Job "job" not found in configuration');

        $instance->createJobByName('job');
    }

    public function testThrowsForUnknownJobType(): void
    {
        $dictionaryBuilders = new ServiceLocator([]);
        $jobBuilders        = new ServiceLocator([]);
        $configuration      = new Configuration();
        $logger             = $this->getMockForAbstractClass(LoggerInterface::class);
        $jobDefinition      = new Definition('job', ['type' => 'test']);

        $configuration->setJob($jobDefinition);

        $instance = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown job type \'test\'');

        $instance->createJobByName('job');
    }

    public function testGetJob(): void
    {
        $jobBuilder         = $this->getMockForAbstractClass(JobBuilderInterface::class);
        $dictionaryBuilders = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $jobBuilders        = new ServiceLocator([
            'test' => function () use ($jobBuilder) {
                return $jobBuilder;
            }
        ]);
        $logger = $this->getMockForAbstractClass(LoggerInterface::class);
        $configuration      = new Configuration();
        $jobDefinition      = new Definition('job', ['type' => 'test']);
        $job                = $this->getMockForAbstractClass(TranslationJobInterface::class);

        $configuration->setJob($jobDefinition);
        $instance = new JobFactory($dictionaryBuilders, $jobBuilders, $configuration, $logger);
        $jobBuilder->expects($this->once())->method('build')->with($instance, $jobDefinition)->willReturn($job);

        self::assertSame($job, $instance->createJobByName('job'));
    }

    public function testGetDictionary(): void
    {
        $dictionaryBuilder  = $this->getMockForAbstractClass(DictionaryBuilderInterface::class);
        $dictionaryBuilders = new ServiceLocator(['test' => function () use ($dictionaryBuilder) {
            return $dictionaryBuilder;
        }]);
        $jobBuilders        = new ServiceLocator([]);
        $configuration      = new Configuration();
        $logger             = $this->getMockForAbstractClass(LoggerInterface::class);
        $dictionary         = $this->getMockForAbstractClass(DictionaryInterface::class);
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

    public function testGetDictionaryWillFallBackToDefaultOnUnknownType(): void
    {
        $dictionaryBuilder  = $this->getMockForAbstractClass(DictionaryBuilderInterface::class);
        $dictionaryBuilders = new ServiceLocator(['default' => function () use ($dictionaryBuilder) {
            return $dictionaryBuilder;
        }]);
        $jobBuilders        = new ServiceLocator([]);
        $configuration      = new Configuration();
        $logger             = $this->getMockForAbstractClass(LoggerInterface::class);
        $dictionary         = $this->getMockForAbstractClass(DictionaryInterface::class);
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

    public function testGetDictionaryForWrite(): void
    {
        $dictionaryBuilder  = $this->getMockForAbstractClass(DictionaryBuilderInterface::class);
        $dictionaryBuilders = new ServiceLocator(['test' => function () use ($dictionaryBuilder) {
            return $dictionaryBuilder;
        }]);
        $jobBuilders        = new ServiceLocator([]);
        $configuration      = new Configuration();
        $logger             = $this->getMockForAbstractClass(LoggerInterface::class);
        $dictionary         = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
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
