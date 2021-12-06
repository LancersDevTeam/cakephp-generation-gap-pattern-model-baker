<?php

declare(strict_types=1);

namespace GenerationGapModelBaker\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use GenerationGapModelBaker\Command\ExtendedModelCommand;

/**
 * GenerationGapModelBaker\Command\ExtendedModelCommand Test Case
 *
 * @uses \GenerationGapModelBaker\Command\ExtendedModelCommand
 */
class ExtendedModelCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }
    /**
     * Test buildOptionParser method
     *
     * @return void
     */
    public function testBuildOptionParser(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test execute method
     *
     * @return void
     */
    public function testExecute(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
