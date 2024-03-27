<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Configuration\Definition\ReferencedJobDefinition */
class ReferencedJobDefinitionTest extends TestCase
{
    public function testAllIsWorking(): void
    {
        $definition = new ReferencedJobDefinition(
            'foo',
            $configuration = new Configuration(),
            $data = ['a' => 'value']
        );

        $configuration->setJob($referenced = new Definition('foo'));

        self::assertSame('foo', $definition->getName());
        self::assertSame($data, $definition->getData());
        self::assertSame($referenced, $definition->getDelegated());
    }
}
