<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Exception;

/**
 * This exception should be thrown when ever a dictionary does not support an operation (add/remove/getWritable/...).
 *
 * @api
 */
final class NotSupportedException extends AbstractDictionaryException
{
}
