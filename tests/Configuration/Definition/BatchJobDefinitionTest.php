<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition */
class BatchJobDefinitionTest extends TestCase
{
    public function testAllIsWorking(): void
    {
        $definition = new BatchJobDefinition(
            'foo',
            $jobs = ['job' => new Definition('bar')],
            $data = ['a' => 'value']
        );

        self::assertSame('foo', $definition->getName());
        self::assertSame($data, $definition->getData());
        self::assertSame($jobs, $definition->getJobs());
    }
}
