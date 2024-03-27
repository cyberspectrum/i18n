<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ExtendedDictionaryDefinition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\CompoundDictionaryDefinitionBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Configuration\DefinitionBuilder\CompoundDictionaryDefinitionBuilder */
class CompoundDictionaryDefinitionBuilderTest extends TestCase
{
    public function throwsForMissingKeyProvider(): array
    {
        return [
            'name'   => ['name', []],
            'dictionaries' => ['dictionaries', ['name' => 'foo']],
        ];
    }

    /**
     * Test that building throws when key is missing.
     *
     * @param string $key The key to expect.
     * @param array  $data
     *
     * @dataProvider throwsForMissingKeyProvider
     */
    public function testThrowsForMissingKey(string $key, array $data): void
    {
        $builder = new CompoundDictionaryDefinitionBuilder(
            $this->getMockBuilder(DefinitionBuilder::class)->disableOriginalConstructor()->getMock()
        );

        $configuration = new Configuration();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "' . $key . '"');

        $builder->build($configuration, $data);
    }

    public function testBuildingForDelegated(): void
    {
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildDictionary'])
            ->getMock();
        $definitionBuilder->expects($this->never())->method('buildDictionary');

        $configuration = new Configuration();

        $configuration->setDictionary(new DictionaryDefinition('base-dict1'));
        $configuration->setDictionary(new DictionaryDefinition('base-dict2'));

        $builder = new DefinitionBuilder\CompoundDictionaryDefinitionBuilder($definitionBuilder);

        $dictionary = $builder->build($configuration, [
            'type'   => 'compound',
            'name'   => 'test',
            'dictionaries' => ['base-dict1', 'base-dict2']
        ]);

        self::assertInstanceOf(DictionaryDefinition::class, $dictionary);
        self::assertCount(2, $dictionaries = $dictionary->get('dictionaries'));
        self::assertInstanceOf(ExtendedDictionaryDefinition::class, $dictionaries[0]);
        self::assertSame('base-dict1', $dictionaries[0]->getName());
        self::assertInstanceOf(ExtendedDictionaryDefinition::class, $dictionaries[1]);
        self::assertSame('base-dict2', $dictionaries[1]->getName());
    }

    public function testBuildForInlined(): void
    {
        $inline            = new DictionaryDefinition('inlined');
        $configuration     = new Configuration();
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildDictionary'])
            ->getMock();
        $definitionBuilder
            ->expects($this->once())
            ->method('buildDictionary')
            ->with($configuration, ['type' => 'inline', 'name' => 'name', 'prefix' => 'prefix'])
            ->willReturn($inline);

        $builder = new CompoundDictionaryDefinitionBuilder($definitionBuilder);

        $dictionary = $builder->build($configuration, [
            'type'   => 'compound',
            'name'   => 'test',
            'dictionaries' => ['name' => ['type' => 'inline', 'prefix' => 'prefix']]
        ]);

        self::assertInstanceOf(DictionaryDefinition::class, $dictionary);
        /** @var DictionaryDefinition $dictionary */
        self::assertCount(1, $jobs = $dictionary->get('dictionaries'));
        self::assertSame($inline, $jobs[0]);
    }

    public function testBuildForInlinedWithoutNameAndPrefix(): void
    {
        $inline            = new DictionaryDefinition('inlined');
        $configuration     = new Configuration();
        $definitionBuilder = $this
            ->getMockBuilder(DefinitionBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildDictionary'])
            ->getMock();
        $definitionBuilder
            ->expects($this->once())
            ->method('buildDictionary')
            ->with($configuration, ['type' => 'inline', 'name' => 'name', 'prefix' => 'name'])
            ->willReturn($inline);

        $builder = new CompoundDictionaryDefinitionBuilder($definitionBuilder);

        $dictionary = $builder->build($configuration, [
            'type'   => 'compound',
            'name'   => 'test',
            'dictionaries' => ['name' => ['type' => 'inline']]
        ]);

        self::assertInstanceOf(DictionaryDefinition::class, $dictionary);
        /** @var DictionaryDefinition $dictionary */
        self::assertCount(1, $jobs = $dictionary->get('dictionaries'));
        self::assertSame($inline, $jobs[0]);
    }
}
