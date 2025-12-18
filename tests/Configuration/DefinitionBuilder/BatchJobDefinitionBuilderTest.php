<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\BatchJobDefinitionBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(BatchJobDefinitionBuilder::class)]
class BatchJobDefinitionBuilderTest extends TestCase
{
    public static function throwsForMissingKeyProvider(): array
    {
        return [
            'name'   => ['name', []],
            'jobs' => ['jobs', ['name' => 'foo']],
        ];
    }

    /**
     * Test that building throws when key is missing.
     *
     * @param string $key The key to expect.
     * @param array  $data
     */
    #[DataProvider('throwsForMissingKeyProvider')]
    public function testThrowsForMissingKey(string $key, array $data): void
    {
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $definitionBuilder->expects($this->never())->method('buildDictionary');
        $definitionBuilder->expects($this->never())->method('buildJob');
        $builder = new BatchJobDefinitionBuilder($definitionBuilder);

        $configuration = new Configuration();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "' . $key . '"');

        $builder->build($configuration, $data);
    }

    public function testBuildForDelegated(): void
    {
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $definitionBuilder->expects($this->never())->method('buildDictionary');
        $definitionBuilder->expects($this->never())->method('buildJob');

        $builder = new BatchJobDefinitionBuilder($definitionBuilder);

        $configuration = new Configuration();
        $configuration->setJob($job1 = new Definition('base-job1'));
        $configuration->setJob($job2 = new Definition('base-job2'));

        $configuration->setDictionary(new DictionaryDefinition('source'));
        $configuration->setDictionary(new DictionaryDefinition('target'));

        $job = $builder->build($configuration, [
            'type'   => 'batch',
            'name'   => 'test',
            'jobs' => ['base-job1', 'base-job2']
        ]);

        self::assertInstanceOf(BatchJobDefinition::class, $job);
        self::assertCount(2, $jobs = $job->getJobs());
        /** @var list<ReferencedJobDefinition> $jobs */
        self::assertInstanceOf(ReferencedJobDefinition::class, $jobs[0]);
        self::assertSame($job1, $jobs[0]->getDelegated());
        self::assertInstanceOf(ReferencedJobDefinition::class, $jobs[1]);
        self::assertSame($job2, $jobs[1]->getDelegated());
    }

    public function testBuildForInlineJob(): void
    {
        $inline            = new Definition('inlined');
        $configuration     = new Configuration();
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildJob'])
            ->getMock();
        $definitionBuilder
            ->expects($this->once())
            ->method('buildJob')
            ->with($configuration, ['type' => 'inline', 'name' => 'test.0'])
            ->willReturn($inline);

        $builder = new BatchJobDefinitionBuilder($definitionBuilder);


        $job = $builder->build($configuration, [
            'type'   => 'batch',
            'name'   => 'test',
            'jobs' => [['type' => 'inline']]
        ]);

        self::assertInstanceOf(BatchJobDefinition::class, $job);
        self::assertCount(1, $jobs = $job->getJobs());
        self::assertSame($inline, $jobs[0]);
    }
}
