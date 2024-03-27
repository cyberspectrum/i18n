<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Definition\CopyJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Configuration\Definition\CopyJobDefinition */
class CopyJobDefinitionTest extends TestCase
{
    public function testAllIsWorking(): void
    {
        $definition = new CopyJobDefinition(
            'foo',
            $source = new DictionaryDefinition('source'),
            $target = new DictionaryDefinition('source'),
            $data = ['a' => 'value']
        );

        self::assertSame('foo', $definition->getName());
        self::assertSame($data, $definition->getData());
        self::assertSame($source, $definition->getSource());
        self::assertSame($target, $definition->getTarget());
    }
}
