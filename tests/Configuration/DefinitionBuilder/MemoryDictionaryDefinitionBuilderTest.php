<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\MemoryDictionaryDefinitionBuilder;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Configuration\DefinitionBuilder\MemoryDictionaryDefinitionBuilder */
class MemoryDictionaryDefinitionBuilderTest extends TestCase
{
    public function testBuilding(): void
    {
        $configuration = new Configuration();

        $configuration->setDictionary(new DictionaryDefinition('base-dict1'));
        $configuration->setDictionary(new DictionaryDefinition('base-dict2'));

        $builder = new MemoryDictionaryDefinitionBuilder();

        $dictionary = $builder->build($configuration, [
            'type'   => 'memory',
            'name'   => 'test',
        ]);

        self::assertInstanceOf(DictionaryDefinition::class, $dictionary);
        self::assertSame('test', $dictionary->getName());
    }
}
