<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Configuration\Configuration */
class ConfigurationTest extends TestCase
{
    public function testEmpty(): void
    {
        $configuration = new Configuration();

        self::assertFalse($configuration->hasJob('test'));
        self::assertFalse($configuration->hasDictionary('test'));
        self::assertSame([], $configuration->getJobNames());
        self::assertSame([], $configuration->getDictionaryNames());
    }

    public function testGetDictionaryThrowsForUnknown(): void
    {
        $configuration = new Configuration();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Dictionary not found: test');

        $configuration->getDictionary('test');
    }

    public function testGetJobThrowsForUnknown(): void
    {
        $configuration = new Configuration();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Job not found: test');

        $configuration->getJob('test');
    }

    public function testGetJob(): void
    {
        $configuration = new Configuration();

        $configuration->setJob($definition = new Definition('test'));

        self::assertTrue($configuration->hasJob('test'));
        self::assertSame($definition, $configuration->getJob('test'));
        self::assertSame(['test'], $configuration->getJobNames());
    }

    public function testGetDictionary(): void
    {
        $configuration = new Configuration();
        $configuration->setDictionary($definition = new Definition('test'));

        self::assertTrue($configuration->hasDictionary('test'));
        self::assertSame($definition, $configuration->getDictionary('test'));
        self::assertSame(['test'], $configuration->getDictionaryNames());
    }
}
