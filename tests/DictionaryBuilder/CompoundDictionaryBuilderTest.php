<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\DictionaryBuilder;

use CyberSpectrum\I18N\Compound\CompoundDictionary;
use CyberSpectrum\I18N\Configuration\Definition\DecoratedDictionaryDefinition;
use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\DictionaryBuilder\CompoundDictionaryBuilder;
use CyberSpectrum\I18N\Job\JobFactory;
use Generator;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(CompoundDictionaryBuilder::class)]
class CompoundDictionaryBuilderTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testGetDictionary(): void
    {
        $factory         = $this->mockJobFactory();
        $this->getMockBuilder(DictionaryInterface::class)->getMock();
        $childDefinition = new DictionaryDefinition(
            'child',
            [
                'prefix' => 'something',
            ]
        );
        $definition      = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
                'dictionaries'    => [$childDefinition]
            ]
        );

        $childDictionary = $this->getMockBuilder(DictionaryInterface::class)->getMock();
        $childDictionary->expects($this->once())->method('getSourceLanguage')->willReturn('en');
        $childDictionary->expects($this->once())->method('getTargetLanguage')->willReturn('de');
        $childDictionary->expects($this->once())->method('keys')->willReturn($this->arrayAsGenerator(['key']));

        $factory
            ->expects($this->once())
            ->method('createDictionary')
            ->willReturnCallback(function ($dictionary) use ($childDictionary) {
                self::assertInstanceOf(DecoratedDictionaryDefinition::class, $dictionary);
                self::assertSame('en', $dictionary->getSourceLanguage());
                self::assertSame('de', $dictionary->getTargetLanguage());

                return $childDictionary;
            });

        $instance = new CompoundDictionaryBuilder();

        self::assertInstanceOf(CompoundDictionary::class, $dictionary = $instance->build($factory, $definition));
        self::assertSame(['something.key'], iterator_to_array($dictionary->keys()));
    }

    public function testGetDictionaryForWrite(): void
    {
        $factory         = $this->mockJobFactory();
        $childDefinition = new DictionaryDefinition(
            'child',
            [
                'prefix' => 'something',
                'source_language' => 'en',
                'target_language' => 'de',
            ]
        );
        $definition      = new DictionaryDefinition(
            'test',
            [
                'type'            => 'test',
                'additional'      => 'data',
                'source_language' => 'en',
                'target_language' => 'de',
                'dictionaries'    => [$childDefinition]
            ]
        );

        $childDictionary = $this->getMockBuilder(WritableDictionaryInterface::class)->getMock();
        $childDictionary->expects($this->once())->method('getSourceLanguage')->willReturn('en');
        $childDictionary->expects($this->once())->method('getTargetLanguage')->willReturn('de');

        $factory
            ->expects($this->once())
            ->method('createWritableDictionary')
            ->willReturnCallback(function ($dictionary) use ($childDictionary) {
                self::assertInstanceOf(DecoratedDictionaryDefinition::class, $dictionary);
                self::assertSame('en', $dictionary->getSourceLanguage());
                self::assertSame('de', $dictionary->getTargetLanguage());

                return $childDictionary;
            });

        $instance = new CompoundDictionaryBuilder();

        self::assertInstanceOf(CompoundDictionary::class, $instance->buildWritable($factory, $definition));
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

    protected function arrayAsGenerator(array $array): Generator
    {
        yield from $array;
    }
}
