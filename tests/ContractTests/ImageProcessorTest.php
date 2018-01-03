<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace PhUml\ContractTests;

use PHPUnit\Framework\TestCase;
use PhUml\Processors\ImageGenerationFailure;
use PhUml\Processors\ImageProcessor;
use Symfony\Component\Process\Process;

abstract class ImageProcessorTest extends TestCase
{
    abstract function processor(Process $process = null): ImageProcessor;

    /** @test */
    function it_knows_its_an_invalid_initial_processor()
    {
        $processor = $this->processor();

        $isInitial = $processor->isInitial();

        $this->assertFalse($isInitial);
    }

    /** @test */
    function it_accepts_dot_language_as_input_and_produces_a_png_image_as_output()
    {
        $processor = $this->processor();

        $input = $processor->getInputType();
        $output = $processor->getOutputType();

        $this->assertEquals('text/dot', $input);
        $this->assertEquals('image/png', $output);
    }

    /** @test */
    function it_generates_an_image_from_a_dot_file()
    {
        $dotFilePath = __DIR__ . '/../.fixtures/classes.dot';
        $classDiagramPath = __DIR__ . '/../.output/diagram.png';
        if (file_exists($classDiagramPath)) {
            unlink($classDiagramPath);
        }

        $this->processor()->execute($dotFilePath, $classDiagramPath);

        $this->assertFileExists($classDiagramPath);
    }

    /** @test */
    function it_provides_feedback_when_the_call_to_the_command_fails()
    {
        $process = new class('unknown') extends Process {
            public function getErrorOutput() { return 'Error calling the external command'; }
        };

        $this->expectException(ImageGenerationFailure::class);
        $this->expectExceptionMessage('Error calling the external command');

        $this->processor($process)->execute('wrong_input.dot', 'output.png');
    }
}
