<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Definition\Definition;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

/** @covers \CyberSpectrum\I18N\Configuration\Definition\Definition */
class DefinitionTest extends TestCase
{
    public function testInitialization(): void
    {
        $definition = new Definition('foo', ['initial' => 'value']);

        self::assertSame('foo', $definition->getName());
        self::assertSame(['initial' => 'value'], $definition->getData());
        self::assertTrue($definition->has('initial'));
        self::assertSame('value', $definition->get('initial'));
        self::assertFalse($definition->has('undefined'));
        self::assertSame(['initial' => 'value'], iterator_to_array($definition->getIterator()));
    }

    public function testGetThrowsForUnknownKeys(): void
    {
        $definition = new Definition('foo');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Key "unknown" does not exist.');

        $definition->get('unknown');
    }
}
