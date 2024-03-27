<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Definition\Definition;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function iterator_to_array;

/** @covers \CyberSpectrum\I18N\Configuration\Definition\ExtendedDefinitionTrait */
class ExtendedDefinitionTraitTest extends TestCase
{
    public function testInitialization(): void
    {
        $definition = $this->mockExtendedDefinition('foo', ['local' => 'local-value'], ['base' => 'base-value']);

        self::assertSame(['base' => 'base-value', 'local' => 'local-value'], $definition->getData());
        self::assertTrue($definition->has('base'));
        self::assertTrue($definition->has('local'));
        self::assertSame('base-value', $definition->get('base'));
        self::assertSame('local-value', $definition->get('local'));
        self::assertFalse($definition->has('undefined'));
        self::assertSame(
            ['base' => 'base-value', 'local' => 'local-value'],
            iterator_to_array($definition->getIterator())
        );
    }

    public function testReturnsFromBaseIfNotSet(): void
    {
        $definition = $this->mockExtendedDefinition('foo', [], ['key' => 'value']);

        self::assertSame('value', $definition->get('key'));
    }

    public function testGetDataMergesArrays(): void
    {
        $definition = $this->mockExtendedDefinition(
            'foo',
            ['key' => 'value', 'array' => ['local' => 'value-local']],
            ['key' => 'value-overridden', 'array' => ['base' => 'value-base']]
        );

        self::assertSame(
            ['key' => 'value', 'array' => ['base' => 'value-base', 'local' => 'value-local']],
            $definition->getData()
        );
    }

    public function testGetDataThrowsForInvalidArrayMerge(): void
    {
        $definition = $this->mockExtendedDefinition('foo', ['key' => ['key1' => 'value']], ['key' => 'value']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Can not merge key "key", parent value is not an array.');

        $definition->getData();
    }

    /**
     * Mock an extended definition.
     *
     * @param string $name     The name.
     * @param array  $data     The data.
     * @param array  $baseData The data for the delegator.
     *
     * @return ExtendedDefinitionTraitMock
     */
    private function mockExtendedDefinition(
        string $name,
        array $data = [],
        array $baseData = []
    ): ExtendedDefinitionTraitMock {
        return new ExtendedDefinitionTraitMock($name, new Definition($name, $baseData), $data);
    }
}
