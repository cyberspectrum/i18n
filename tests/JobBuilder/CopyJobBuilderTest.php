<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\JobBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\CopyJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition;
use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Job\CopyDictionaryJob;
use CyberSpectrum\I18N\Job\JobFactory;
use CyberSpectrum\I18N\JobBuilder\CopyJobBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(CopyJobBuilder::class)]
class CopyJobBuilderTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testBuild(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockBuilder(DictionaryInterface::class)->getMock());
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockBuilder(WritableDictionaryInterface::class)->getMock());

        $definition = new CopyJobDefinition('test', $source, $target);

        $instance = new CopyJobBuilder();

        self::assertInstanceOf(CopyDictionaryJob::class, $job = $instance->build($builder, $definition));

        self::assertSame(CopyDictionaryJob::COPY_IF_EMPTY, $job->getCopySource());
        self::assertSame(CopyDictionaryJob::COPY_IF_EMPTY, $job->getCopyTarget());
        self::assertFalse($job->hasRemoveObsolete());
        self::assertFalse($job->isDryRun());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testBuildWithOverrides(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockBuilder(DictionaryInterface::class)->getMock());
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockBuilder(WritableDictionaryInterface::class)->getMock());

        $definition = new CopyJobDefinition('test', $source, $target, [
            'copy-source'    => true,
            'copy-target'    => true,
            'remove-obsolete' => true,
        ]);

        $instance = new CopyJobBuilder();

        self::assertInstanceOf(CopyDictionaryJob::class, $job = $instance->build($builder, $definition));

        self::assertSame(CopyDictionaryJob::COPY, $job->getCopySource());
        self::assertSame(CopyDictionaryJob::COPY, $job->getCopyTarget());
        self::assertTrue($job->hasRemoveObsolete());
        self::assertFalse($job->isDryRun());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testBuildUnwrapsReferencedJobs(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockBuilder(DictionaryInterface::class)->getMock());
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockBuilder(WritableDictionaryInterface::class)->getMock());

        $configuration = new Configuration();
        $configuration->setJob(new CopyJobDefinition('test', $source, $target, [
            'copy-source'    => true,
            'copy-target'    => true,
            'remove-obsolete' => true,
        ]));

        $definition = new ReferencedJobDefinition('test', $configuration);

        $instance = new CopyJobBuilder();
        self::assertInstanceOf(CopyDictionaryJob::class, $job = $instance->build($builder, $definition));

        self::assertSame(CopyDictionaryJob::COPY, $job->getCopySource());
        self::assertSame(CopyDictionaryJob::COPY, $job->getCopyTarget());
        self::assertTrue($job->hasRemoveObsolete());
        self::assertFalse($job->isDryRun());
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
        $instance   = new CopyJobBuilder();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid definition passed.');

        $instance->build($builder, $definition);
    }

    /**
     * Data provider for the flag conversion test.
     */
    public static function stringToFlagProvider(): array
    {
        return [
            [CopyDictionaryJob::COPY, 'true'],
            [CopyDictionaryJob::COPY, true],
            [CopyDictionaryJob::COPY, 'yes'],
            [CopyDictionaryJob::DO_NOT_COPY, 'no'],
            [CopyDictionaryJob::DO_NOT_COPY, 'false'],
            [CopyDictionaryJob::DO_NOT_COPY, false],
            [CopyDictionaryJob::COPY_IF_EMPTY, 'if-empty'],
        ];
    }

    /**
     * Test.
     *
     * @param int   $expected The expected result.
     * @param mixed $input    The input value.
     */
    #[DataProvider('stringToFlagProvider')]
    #[AllowMockObjectsWithoutExpectations]
    public function testStringToFlag(int $expected, $input): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockBuilder(DictionaryInterface::class)->getMock());
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockBuilder(WritableDictionaryInterface::class)->getMock());

        $definition = new CopyJobDefinition('test', $source, $target, ['copy-source' => $input]);

        $instance = new CopyJobBuilder();

        self::assertInstanceOf(CopyDictionaryJob::class, $job = $instance->build($builder, $definition));

        self::assertSame($expected, $job->getCopySource());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testInvalidStringToFlagThrows(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockBuilder(DictionaryInterface::class)->getMock());
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockBuilder(WritableDictionaryInterface::class)->getMock());

        $definition = new CopyJobDefinition('test', $source, $target, ['copy-source' => 'invalid']);

        $instance = new CopyJobBuilder();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value for copy flag.');

        $instance->build($builder, $definition);
    }

    /** Data provider for the flag conversion test. */
    public static function boolishToFlagProvider(): array
    {
        return [
            [true, 'true'],
            [true, true],
            [true, 'yes'],
            [false, 'no'],
            [false, 'false'],
            [false, false],
        ];
    }

    /**
     * Test.
     *
     * @param bool  $expected The expected result.
     * @param mixed $input    The input value.
     */
    #[DataProvider('boolishToFlagProvider')]
    #[AllowMockObjectsWithoutExpectations]
    public function testBoolishToFlag(bool $expected, $input): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockBuilder(DictionaryInterface::class)->getMock());
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockBuilder(WritableDictionaryInterface::class)->getMock());

        $definition = new CopyJobDefinition('test', $source, $target, ['remove-obsolete' => $input]);

        $instance = new CopyJobBuilder();

        self::assertInstanceOf(CopyDictionaryJob::class, $job = $instance->build($builder, $definition));

        self::assertSame($expected, $job->hasRemoveObsolete());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testInvalidSBoolishToFlagThrows(): void
    {
        $builder = $this
            ->getMockBuilder(JobFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $source = new DictionaryDefinition('source');
        $target = new DictionaryDefinition('target');

        $builder
            ->expects($this->once())
            ->method('createDictionary')
            ->with($source)
            ->willReturn($this->getMockBuilder(DictionaryInterface::class)->getMock());
        $builder
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->with($target)
            ->willReturn($this->getMockBuilder(WritableDictionaryInterface::class)->getMock());

        $definition = new CopyJobDefinition('test', $source, $target, ['remove-obsolete' => 'invalid']);

        $instance = new CopyJobBuilder();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value for remove-obsolete flag.');

        $instance->build($builder, $definition);
    }
}
