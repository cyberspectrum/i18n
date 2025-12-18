<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Job;

use CyberSpectrum\I18N\Memory\MemoryDictionary;
use CyberSpectrum\I18N\Job\CopyDictionaryJob;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(CopyDictionaryJob::class)]
class CopyDictionaryJobTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testCopyBoth(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource()
            ->setCopyTarget()
            ->run();

        $reader = $targetDictionary->get('test-key1');
        self::assertSame('Source 1', $reader->getSource());
        self::assertSame('Target 1', $reader->getTarget());

        $reader = $targetDictionary->get('test-key2');
        self::assertSame('Source 2', $reader->getSource());
        self::assertSame('Target 2', $reader->getTarget());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testCopySource(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource()
            ->setCopyTarget(CopyDictionaryJob::DO_NOT_COPY)
            ->run();

        $reader = $targetDictionary->get('test-key1');
        self::assertSame('Source 1', $reader->getSource());
        self::assertSame('-old-value-', $reader->getTarget());

        $reader = $targetDictionary->get('test-key2');
        self::assertSame('Source 2', $reader->getSource());
        self::assertNull($reader->getTarget());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testCopyTarget(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource(CopyDictionaryJob::DO_NOT_COPY)
            ->setCopyTarget()
            ->run();

        $reader = $targetDictionary->get('test-key1');
        self::assertSame('-old-value-', $reader->getSource());
        self::assertSame('Target 1', $reader->getTarget());

        $reader = $targetDictionary->get('test-key2');
        self::assertNull($reader->getSource());
        self::assertSame('Target 2', $reader->getTarget());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testCopyIfEmpty(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
            'test-key3' => [
                'source' => 'Source 3',
                'target' => 'Target 3',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
            'test-key3' => [
                'source' => '',
                'target' => '',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource(CopyDictionaryJob::COPY_IF_EMPTY)
            ->setCopyTarget(CopyDictionaryJob::COPY_IF_EMPTY)
            ->run();

        $reader = $targetDictionary->get('test-key1');
        self::assertSame('-old-value-', $reader->getSource());
        self::assertSame('-old-value-', $reader->getTarget());

        $reader = $targetDictionary->get('test-key2');
        self::assertSame('Source 2', $reader->getSource());
        self::assertSame('Target 2', $reader->getTarget());

        $reader = $targetDictionary->get('test-key3');
        self::assertSame('Source 3', $reader->getSource());
        self::assertSame('Target 3', $reader->getTarget());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testRemoveObsolete(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource()
            ->setCopyTarget()
            ->setRemoveObsolete()
            ->run();

        $reader = $targetDictionary->get('test-key1');
        self::assertSame('Source 1', $reader->getSource());
        self::assertSame('Target 1', $reader->getTarget());

        self::assertFalse($targetDictionary->has('test-key2'));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testDryRunDoesNothing(): void
    {
        $sourceDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => 'Source 1',
                'target' => 'Target 1',
            ],
            'test-key3' => [
                'source' => 'Source 3',
                'target' => 'Target 3',
            ],
        ]);

        $targetDictionary = new MemoryDictionary('en', 'de', [
            'test-key1' => [
                'source' => '-old-value-',
                'target' => '-old-value-',
            ],
            'test-key2' => [
                'source' => 'Source 2',
                'target' => 'Target 2',
            ],
        ]);

        CopyDictionaryJob::create($sourceDictionary, $targetDictionary, $this->mockLogger())
            ->setCopySource()
            ->setCopyTarget()
            ->setRemoveObsolete()
            ->setDryRun()
            ->run();

        $reader = $targetDictionary->get('test-key1');
        self::assertSame('-old-value-', $reader->getSource());
        self::assertSame('-old-value-', $reader->getTarget());
        $reader = $targetDictionary->get('test-key2');
        self::assertSame('Source 2', $reader->getSource());
        self::assertSame('Target 2', $reader->getTarget());
        self::assertFalse($targetDictionary->has('test-key3'));
    }

    /**
     * Mock a logger.
     *
     * @return MockObject|LoggerInterface
     */
    private function mockLogger()
    {
        return $this->getMockBuilder(LoggerInterface::class)->getMock();
    }
}
