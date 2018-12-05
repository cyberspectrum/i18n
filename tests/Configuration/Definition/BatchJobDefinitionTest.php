<?php

/**
 * This file is part of cyberspectrum/i18n.
 *
 * (c) 2018 CyberSpectrum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    cyberspectrum/i18n
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2018 CyberSpectrum.
 * @license    https://github.com/cyberspectrum/i18n/blob/master/LICENSE MIT
 * @filesource
 */

declare(strict_types = 1);

namespace CyberSpectrum\I18N\Test\Configuration\Definition;

use CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use PHPUnit\Framework\TestCase;

/**
 * This tests the batch job definition.
 *
 * @covers \CyberSpectrum\I18N\Configuration\Definition\BatchJobDefinition
 */
class BatchJobDefinitionTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testAllIsWorking(): void
    {
        $definition = new BatchJobDefinition(
            'foo',
            $jobs = ['job' => new Definition('bar')],
            $data = ['a' => 'value']
        );

        $this->assertSame('foo', $definition->getName());
        $this->assertSame($data, $definition->getData());
        $this->assertSame($jobs, $definition->getJobs());
    }
}
