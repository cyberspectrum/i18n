<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration;

use CyberSpectrum\I18N\Configuration\AbstractConfigurationLoader;
use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\LoaderInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Configuration\AbstractConfigurationLoader */
class AbstractConfigurationLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $factory = $this->getMockForAbstractClass(AbstractConfigurationLoader::class);
        $configuration = new Configuration();

        $loader = $this->getMockForAbstractClass(LoaderInterface::class);
        $loader->expects($this->once())->method('supports')->with('source')->willReturn(true);
        $loader->expects($this->once())->method('load')->with('source');

        $factory
            ->expects($this->once())
            ->method('getLoader')
            ->with('source', $configuration)
            ->willReturn($loader);

        self::assertSame($configuration, $factory->load('source', $configuration));
    }

    public function testThrowsForUnsupported(): void
    {
        $factory = $this->getMockForAbstractClass(AbstractConfigurationLoader::class);
        $configuration = new Configuration();

        $loader = $this->getMockForAbstractClass(LoaderInterface::class);
        $loader->expects($this->once())->method('supports')->with('source')->willReturn(false);
        $loader->expects($this->never())->method('load');

        $factory
            ->expects($this->once())
            ->method('getLoader')
            ->with('source', $configuration)
            ->willReturn($loader);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported configuration.');

        self::assertSame($configuration, $factory->load('source', $configuration));
    }

    public function testCreatesConfigIfNotGiven(): void
    {
        $factory = $this->getMockForAbstractClass(AbstractConfigurationLoader::class);

        $loader = $this->getMockForAbstractClass(LoaderInterface::class);
        $loader->expects($this->once())->method('supports')->with('source')->willReturn(false);
        $loader->expects($this->never())->method('load');

        $factory
            ->expects($this->once())
            ->method('getLoader')
            ->with('source')
            ->willReturn($loader);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported configuration.');

        self::assertInstanceOf(Configuration::class, $factory->load('source'));
    }
}
