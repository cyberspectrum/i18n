<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\DictionaryBuilder;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\DictionaryProviderInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryProviderInterface;
use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\DictionaryBuilder\DefaultDictionaryBuilder;
use CyberSpectrum\I18N\Job\JobFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use UnexpectedValueException;

/** @covers \CyberSpectrum\I18N\DictionaryBuilder\DefaultDictionaryBuilder */
class DefaultDictionaryBuilderTest extends TestCase
{
    public function testGetDictionary(): void
    {
        $provider   = $this->getMockForAbstractClass(DictionaryProviderInterface::class);
        $providers  = new ServiceLocator(['test' => function () use ($provider) {
            return $provider;
        }]);
        $dictionary = $this->getMockForAbstractClass(DictionaryInterface::class);
        $definition = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $instance = new DefaultDictionaryBuilder($providers);

        $provider
            ->expects($this->once())
            ->method('getDictionary')
            ->with(
                'test',
                'en',
                'de',
                [
                    'type'            => 'test',
                    'additional'      => 'data',
                    'source_language' => 'en',
                    'target_language' => 'de',
                ]
            )
            ->willReturn($dictionary);

        self::assertSame($dictionary, $instance->build($this->mockJobFactory(), $definition));
    }

    public function testGetDictionaryThrowsForWriteOnlyProvider(): void
    {
        $provider   = $this->getMockForAbstractClass(WritableDictionaryProviderInterface::class);
        $providers  = new ServiceLocator(['test' => function () use ($provider) {
            return $provider;
        }]);
        $definition = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Dictionary provider "test" can not create readable dictionaries.');

        $instance = new DefaultDictionaryBuilder($providers);
        $instance->build($this->mockJobFactory(), $definition);
    }

    public function testGetDictionaryThrowsForUnknownProvider(): void
    {
        $providers  = new ServiceLocator([]);
        $definition = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('No provider named "test" registered.');

        $instance = new DefaultDictionaryBuilder($providers);
        $instance->build($this->mockJobFactory(), $definition);
    }

    public function testGetDictionaryForWriteThrowsForReadOnlyProvider(): void
    {
        $provider   = $this->getMockForAbstractClass(DictionaryProviderInterface::class);
        $providers  = new ServiceLocator(['test' => function () use ($provider) {
            return $provider;
        }]);
        $definition = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Dictionary provider "test" can not create writable dictionaries.');

        $instance = new DefaultDictionaryBuilder($providers);
        $instance->buildWritable($this->mockJobFactory(), $definition);
    }

    public function testGetDictionaryForWriteThrowsForUnknownProvider(): void
    {
        $providers  = new ServiceLocator([]);
        $definition = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('No provider named "test" registered.');

        $instance = new DefaultDictionaryBuilder($providers);
        $instance->buildWritable($this->mockJobFactory(), $definition);
    }

    public function testGetDictionaryForWrite(): void
    {
        $provider      = $this->getMockForAbstractClass(WritableDictionaryProviderInterface::class);
        $providers     = new ServiceLocator(['test' => function () use ($provider) {
            return $provider;
        }]);
        $dictionary    = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $definition    = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );

        $provider
            ->expects($this->once())
            ->method('getDictionaryForWrite')
            ->with(
                'test',
                'en',
                'de',
                [
                    'type'            => 'test',
                    'additional'      => 'data',
                    'source_language' => 'en',
                    'target_language' => 'de',
                ]
            )
            ->willReturn($dictionary);

        $instance = new DefaultDictionaryBuilder($providers);
        self::assertSame($dictionary, $instance->buildWritable($this->mockJobFactory(), $definition));
    }

    public function testGetDictionaryForWriteWillCreateNewIfNotFound(): void
    {
        $provider      = $this->getMockForAbstractClass(WritableDictionaryProviderInterface::class);
        $providers     = new ServiceLocator(['test' => function () use ($provider) {
            return $provider;
        }]);
        $dictionary    = $this->getMockForAbstractClass(WritableDictionaryInterface::class);
        $definition    = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );
        $provider
            ->expects($this->once())
            ->method('getDictionaryForWrite')
            ->with(
                'test',
                'en',
                'de',
                [
                    'type'            => 'test',
                    'additional'      => 'data',
                    'source_language' => 'en',
                    'target_language' => 'de',
                ]
            )
            ->willThrowException(new DictionaryNotFoundException('test', 'en', 'de'));

        $provider
            ->expects($this->once())
            ->method('createDictionary')
            ->with(
                'test',
                'en',
                'de',
                [
                    'type'            => 'test',
                    'additional'      => 'data',
                    'source_language' => 'en',
                    'target_language' => 'de',
                ]
            )
            ->willReturn($dictionary);

        $instance = new DefaultDictionaryBuilder($providers);
        self::assertSame($dictionary, $instance->buildWritable($this->mockJobFactory(), $definition));
    }

    /**
     * Mock a job factory.
     *
     * @return MockObject|JobFactory
     */
    private function mockJobFactory(): MockObject
    {
        return $this->getMockBuilder(JobFactory::class)->disableOriginalConstructor()->getMock();
    }
}
