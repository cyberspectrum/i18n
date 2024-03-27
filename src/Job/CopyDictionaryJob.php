<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Job;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\TranslationValue\TranslationValueInterface;
use CyberSpectrum\I18N\TranslationValue\WritableTranslationValueInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use Throwable;

/**
 * This copies the translations from one dictionary to another one.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class CopyDictionaryJob implements TranslationJobInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** Do not copy. */
    public const DO_NOT_COPY = 0;

    /** Copy values. */
    public const COPY = 1;

    /** Copy if empty. */
    public const COPY_IF_EMPTY = 2;

    /** The source dictionary. */
    private DictionaryInterface $sourceDictionary;

    /** The target dictionary. */
    private WritableDictionaryInterface $targetDictionary;

    /** Flag if the source value shall be copied/updated. */
    private int $copySource = self::COPY_IF_EMPTY;

    /** Flag if the target value shall be copied/updated. */
    private int $copyTarget = self::COPY_IF_EMPTY;

    /** Flag if obsolete keys shall be removed. */
    private bool $removeObsolete = false;

    /** Flag if this is a dry run (false => update target, true => log updates with level notice). */
    private bool $dryRun = false;

    /**  The log level to use for informal messages. */
    private string $logLevel;

    /**
     * The regular expressions to filter.
     *
     * @var list<string>
     */
    private array $filters = [];

    /**
     * Create a new instance.
     *
     * @param DictionaryInterface         $sourceDictionary The source dictionary.
     * @param WritableDictionaryInterface $targetDictionary The target dictionary.
     */
    public function __construct(
        DictionaryInterface $sourceDictionary,
        WritableDictionaryInterface $targetDictionary
    ) {
        $this->sourceDictionary = $sourceDictionary;
        $this->targetDictionary = $targetDictionary;
        $this->logger           = null;
        $this->logLevel         = LogLevel::DEBUG;
    }

    /**
     * Static helper for fluent coding.
     *
     * @param DictionaryInterface         $sourceDictionary The source dictionary.
     * @param WritableDictionaryInterface $targetDictionary The target dictionary.
     * @param LoggerInterface|null        $logger           The logger to use.
     */
    public static function create(
        DictionaryInterface $sourceDictionary,
        WritableDictionaryInterface $targetDictionary,
        LoggerInterface $logger = null
    ): CopyDictionaryJob {
        $instance = new self($sourceDictionary, $targetDictionary);
        if ($logger) {
            $instance->setLogger($logger);
        }

        return $instance;
    }

    /** Set copy source flag. */
    public function setCopySource(int $copySource = self::COPY): CopyDictionaryJob
    {
        $this->copySource = $copySource;

        return $this;
    }

    /** Retrieve copy source flag. */
    public function getCopySource(): int
    {
        return $this->copySource;
    }

    /** Set copy target flag. */
    public function setCopyTarget(int $copyTarget = self::COPY): CopyDictionaryJob
    {
        $this->copyTarget = $copyTarget;

        return $this;
    }

    /** Retrieve copy target flag. */
    public function getCopyTarget(): int
    {
        return $this->copyTarget;
    }

    /** Set remove obsolete flag. */
    public function setRemoveObsolete(bool $removeObsolete = true): CopyDictionaryJob
    {
        $this->removeObsolete = $removeObsolete;

        return $this;
    }

    /** Retrieve remove obsolete flag. */
    public function hasRemoveObsolete(): bool
    {
        return $this->removeObsolete;
    }

    /** Set dry run flag. */
    public function setDryRun(bool $dryRun = true): CopyDictionaryJob
    {
        $this->dryRun = $dryRun;

        return $this;
    }

    /** Retrieve dry run flag. */
    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * Add a regular expression.
     *
     * @param string $expression The regular expression matching keys to filter away.
     *
     * @return CopyDictionaryJob
     *
     * @throws InvalidArgumentException When the regex is invalid.
     */
    public function addFilter(string $expression): CopyDictionaryJob
    {
        // Check if the first and last char match - if not, we must encapsulate with '/'.
        if ($expression[0] !== substr($expression, -1)) {
            $expression = '/' . $expression . '/';
        }

        // Test if the regex is valid.
        try {
            if (false === preg_match($expression, '')) {
                $error = preg_last_error();
                throw new RuntimeException([
                    PREG_NO_ERROR => 'PREG_NO_ERROR',
                    PREG_INTERNAL_ERROR => 'PREG_INTERNAL_ERROR',
                    PREG_BACKTRACK_LIMIT_ERROR => 'PREG_BACKTRACK_LIMIT_ERROR',
                    PREG_RECURSION_LIMIT_ERROR => 'PREG_RECURSION_LIMIT_ERROR',
                    PREG_BAD_UTF8_ERROR => 'PREG_BAD_UTF8_ERROR',
                    PREG_BAD_UTF8_OFFSET_ERROR => 'PREG_BAD_UTF8_OFFSET_ERROR',
                    PREG_JIT_STACKLIMIT_ERROR => 'PREG_JIT_STACKLIMIT_ERROR',
                ][$error], preg_last_error());
            }
        } catch (Throwable $error) {
            throw new InvalidArgumentException(
                sprintf('Filter "%s" is not a valid regular expression - Error: %s', $expression, $error->getMessage()),
                0,
                $error
            );
        }

        $this->filters[] = $expression;

        return $this;
    }

    /** Set the filter expressions. */
    public function setFilters(string ...$expressions): CopyDictionaryJob
    {
        $this->filters = [];
        foreach ($expressions as $expression) {
            $this->addFilter($expression);
        }

        return $this;
    }

    /**
     * Obtain the regular expressions.
     *
     * @return list<string>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function run(bool $dryRun = null): void
    {
        $prevDry = $this->dryRun;
        try {
            if (null !== $dryRun) {
                $this->dryRun = $dryRun;
            }

            $this->logLevel = $this->dryRun ? LogLevel::NOTICE : LogLevel::DEBUG;

            foreach ($this->sourceDictionary->keys() as $key) {
                if ($this->isFiltered($key)) {
                    continue;
                }
                $this->copyKey($key);
            }

            if ($this->removeObsolete) {
                $this->cleanTarget();
            }
        } finally {
            $this->dryRun = $prevDry;
        }
    }

    /**
     * Copy a key.
     *
     * @param string $key The key.
     */
    private function copyKey(string $key): void
    {
        $source = $this->sourceDictionary->get($key);
        if ($source->isSourceEmpty()) {
            $this->log(
                LogLevel::DEBUG,
                '{key}: Is empty in source language and therefore skipped.',
                ['key' => $key]
            );
            return;
        }

        if (!$this->targetDictionary->has($key)) {
            $this->log($this->logLevel, 'Adding key {key}.', ['key' => $key]);
            if ($this->dryRun) {
                return;
            }

            $this->targetDictionary->add($key);
        }
        $target = $this->targetDictionary->getWritable($key);

        $this->copySource($source, $target);
        $this->copyTarget($source, $target);
    }

    /**
     * Copy the source value.
     *
     * @param TranslationValueInterface         $source The source value.
     * @param WritableTranslationValueInterface $target The target value.
     */
    private function copySource(TranslationValueInterface $source, WritableTranslationValueInterface $target): void
    {
        if (self::DO_NOT_COPY === $this->copySource) {
            return;
        }

        if (($oldValue = $target->getSource()) === ($newValue = $source->getSource())) {
            $this->log(
                $this->logLevel,
                '{key}: Source is same, no need to update.',
                ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
            );
            return;
        }

        if ((self::COPY_IF_EMPTY === $this->copySource) && !$target->isSourceEmpty()) {
            $this->log(
                $this->logLevel,
                '{key}: Source is not empty, no need to update.',
                ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
            );

            return;
        }

        $this->log(
            LogLevel::NOTICE,
            '{key}: Updating source value.',
            ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
        );

        if ($this->dryRun) {
            return;
        }

        if (null === $newValue) {
            $target->clearSource();
            return;
        }
        $target->setSource($newValue);
    }

    /**
     * Copy the target value.
     *
     * @param TranslationValueInterface         $source The source value.
     * @param WritableTranslationValueInterface $target The target value.
     */
    private function copyTarget(TranslationValueInterface $source, WritableTranslationValueInterface $target): void
    {
        if (self::DO_NOT_COPY === $this->copyTarget) {
            return;
        }

        if (
            (($oldValue = $target->getTarget()) === ($newValue = $source->getTarget()))
            || ($target->isTargetEmpty() && $source->isTargetEmpty())
        ) {
            $this->log(
                $this->logLevel,
                '{key}: Target is same, no need to update.',
                ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
            );
            return;
        }

        if ((self::COPY_IF_EMPTY === $this->copyTarget) && !$target->isTargetEmpty()) {
            $this->log(
                $this->logLevel,
                '{key}: Target is not empty, no need to update.',
                ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
            );

            return;
        }

        $this->log(
            LogLevel::NOTICE,
            '{key}: Updating target value.',
            ['key' => $target->getKey(), 'old' => $oldValue, 'new' => $newValue]
        );

        if ($this->dryRun) {
            return;
        }

        if (null === $value = $source->getTarget()) {
            $target->clearTarget();
            return;
        }
        $target->setTarget($value);
    }

    /** Clean the target language. */
    private function cleanTarget(): void
    {
        foreach ($this->targetDictionary->keys() as $key) {
            if (!$this->sourceDictionary->has($key) || $this->sourceDictionary->get($key)->isSourceEmpty()) {
                $this->log($this->logLevel, 'Removing obsolete {key}.', ['key' => $key]);
                if ($this->dryRun) {
                    continue;
                }

                $this->targetDictionary->remove($key);
            }
        }
    }

    /**
     * Check if the passed key is filtered by any of the regexes.
     *
     * @param string $key The key to check.
     */
    private function isFiltered(string $key): bool
    {
        foreach ($this->filters as $expression) {
            if (preg_match($expression, $key)) {
                $this->log(LogLevel::DEBUG, sprintf('"%1$s" is filtered by "%2$s', $key, $expression));
                return true;
            }
        }

        return false;
    }

    private function log(string $level, string $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }
}
