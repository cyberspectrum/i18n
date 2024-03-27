<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\DefinitionBuilderInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\DependencyInjection\ServiceLocator;

/** @covers \CyberSpectrum\I18N\Configuration\DefinitionBuilder */
class DefinitionBuilderTest extends TestCase
{
    public function testBuildDictionary(): void
    {
        $configuration      = new Configuration();
        $data               = ['type' => 'typeName'];
        $dictionary         = new Definition('dummy');
        $dictionaryBuilder  = $this->getMockForAbstractClass(DefinitionBuilderInterface::class);
        $dictionaryBuilders = new ServiceLocator(['typeName' => function () use ($dictionaryBuilder) {
            return $dictionaryBuilder;
        }]);
        $jobBuilders        = new ServiceLocator([]);

        $dictionaryBuilder
            ->expects($this->once())
            ->method('build')
            ->with($configuration, $data)
            ->willReturn($dictionary);

        $builder = new DefinitionBuilder($dictionaryBuilders, $jobBuilders);

        self::assertSame($dictionary, $builder->buildDictionary($configuration, $data));
    }

    public function testBuildDictionaryThrowsForUnknownType(): void
    {
        $builder = new DefinitionBuilder(new ServiceLocator([]), new ServiceLocator([]));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown dictionary type "typeName"');

        $builder->buildDictionary(new Configuration(), ['type' => 'typeName']);
    }

    public function testBuildJob(): void
    {
        $configuration      = new Configuration();
        $data               = ['type' => 'typeName'];
        $dictionary         = new Definition('dummy');
        $jobBuilder         = $this->getMockForAbstractClass(DefinitionBuilderInterface::class);
        $dictionaryBuilders = new ServiceLocator([]);
        $jobBuilders        = new ServiceLocator(['typeName' => function () use ($jobBuilder) {
            return $jobBuilder;
        }]);

        $jobBuilder
            ->expects($this->once())
            ->method('build')
            ->with($configuration, $data)
            ->willReturn($dictionary);

        $builder = new DefinitionBuilder($dictionaryBuilders, $jobBuilders);

        self::assertSame($dictionary, $builder->buildJob($configuration, $data));
    }

    public function testBuildJobThrowsForUnknownType(): void
    {
        $builder = new DefinitionBuilder(new ServiceLocator([]), new ServiceLocator([]));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown job type "typeName"');

        $builder->buildJob(new Configuration(), ['type' => 'typeName']);
    }
}
