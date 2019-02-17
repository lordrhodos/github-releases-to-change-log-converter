<?php declare(strict_types=1);

namespace Lordrhodos\GithubTools\Tests\Command;

use Lordrhodos\GithubTools\Command\ConvertCommand;
use Lordrhodos\GithubTools\Converter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertCommandTest extends TestCase
{

    public function testConstructor(): void
    {
        $converterMock = $this->createMock(Converter::class);
        $command = new ConvertCommand($converterMock);
        $this->assertInstanceOf(Command::class, $command);
    }
    public function testExecute(): void
    {
        $inputMock = $this->createMock(StringInput::class);
        $inputMock->method('getArgument')->willReturnOnConsecutiveCalls('foo', 'bar');
        $output = $this->createMock(ConsoleOutput::class);
        $output->method('getFormatter')->willReturn(new OutputFormatter());
        $converterMock = $this->createMock(Converter::class);
        $converterMock->method('convert')
                      ->with('foo', 'bar')
                      ->willReturn('Changelog for foo/bar');
        $command = (new class($converterMock) extends ConvertCommand {
              public function execute(InputInterface $input, OutputInterface $output): void
              {
                  parent::execute($input, $output);
              }
        });
        $exitCode = $command->execute($inputMock, $output);

        $this->assertSame(0, (int) $exitCode);
    }
}
