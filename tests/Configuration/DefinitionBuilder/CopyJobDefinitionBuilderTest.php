<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Configuration\DefinitionBuilder;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\ExtendedDictionaryDefinition;
use CyberSpectrum\I18N\Configuration\Definition\CopyJobDefinition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\CopyJobDefinitionBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\Configuration\DefinitionBuilder\CopyJobDefinitionBuilder */
class CopyJobDefinitionBuilderTest extends TestCase
{
    public function throwsForMissingKeyProvider(): array
    {
        return [
            'name'   => ['name', []],
            'source' => ['source', ['name' => 'foo']],
            'target' => ['target', ['name' => 'foo', 'source' => 'source']],
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
        $builder = new CopyJobDefinitionBuilder();

        $configuration = new Configuration();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing key "' . $key . '"');

        $builder->build($configuration, $data);
    }

    public function testBuild(): void
    {
        $builder = new CopyJobDefinitionBuilder();

        $configuration = new Configuration();
        $configuration->setDictionary(new DictionaryDefinition('source'));
        $configuration->setDictionary(new DictionaryDefinition('target'));

        $job = $builder->build($configuration, [
            'type'   => 'copy',
            'name'   => 'test',
            'source' => 'source',
            'target' => 'target',
            'additional_value' => 'foo'
        ]);

        self::assertInstanceOf(CopyJobDefinition::class, $job);
        /** @var CopyJobDefinition $job */
        self::assertSame(['type'   => 'copy','additional_value' => 'foo'], $job->getData());
        self::assertInstanceOf(ExtendedDictionaryDefinition::class, $source = $job->getSource());
        self::assertSame('source', $source->getName());
        self::assertSame([], $source->getData());
        self::assertInstanceOf(ExtendedDictionaryDefinition::class, $target = $job->getTarget());
        self::assertSame('target', $target->getName());
        self::assertSame([], $target->getData());
    }

    public function testBuildWithOverride(): void
    {
        $builder = new CopyJobDefinitionBuilder();

        $configuration = new Configuration();
        $configuration->setDictionary(new DictionaryDefinition('source'));
        $configuration->setDictionary(new DictionaryDefinition('target'));

        $job = $builder->build($configuration, [
            'type'   => 'copy',
            'name'   => 'test',
            'source' => 'source',
            'target' => ['name' => 'target', 'custom' => 'value'],
            'additional_value' => 'foo'
        ]);

        self::assertInstanceOf(CopyJobDefinition::class, $job);
        /** @var CopyJobDefinition $job */
        self::assertSame(['type'   => 'copy','additional_value' => 'foo'], $job->getData());
        self::assertInstanceOf(ExtendedDictionaryDefinition::class, $source = $job->getSource());
        self::assertSame('source', $source->getName());
        self::assertSame([], $source->getData());
        self::assertInstanceOf(ExtendedDictionaryDefinition::class, $target = $job->getTarget());
        self::assertSame('target', $target->getName());
        self::assertSame(['custom' => 'value'], $target->getData());
    }

    public function testBuildWithOverriddenLanguages(): void
    {
        $builder = new CopyJobDefinitionBuilder();

        $configuration = new Configuration();
        $configuration->setDictionary(new DictionaryDefinition('source'));
        $configuration->setDictionary(new DictionaryDefinition('target'));

        $job = $builder->build($configuration, [
            'type'   => 'copy',
            'name'   => 'test',
            'source' => 'source',
            'target' => ['name' => 'target', 'custom' => 'value'],
            'source_language' => 'fr',
            'target_language' => 'de',
        ]);

        self::assertInstanceOf(CopyJobDefinition::class, $job);
        /** @var CopyJobDefinition $job */
        $source = $job->getSource();
        $target = $job->getTarget();

        self::assertSame('fr', $source->getSourceLanguage());
        self::assertSame('de', $source->getTargetLanguage());
        self::assertSame('fr', $target->getSourceLanguage());
        self::assertSame('de', $target->getTargetLanguage());
    }

    public function testThrowsForMissingNameInOverride(): void
    {
        $builder = new CopyJobDefinitionBuilder();

        $configuration = new Configuration();
        $configuration->setDictionary(new DictionaryDefinition('source'));
        $configuration->setDictionary(new DictionaryDefinition('target'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Dictionary "test.target" information is missing key "name".');

        $builder->build($configuration, [
            'type'   => 'copy',
            'name'   => 'test',
            'source' => 'source',
            'target' => ['custom' => 'value']
        ]);
    }
}
