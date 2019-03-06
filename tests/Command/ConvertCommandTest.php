<?php declare(strict_types=1);

namespace Lordrhodos\GithubTools\Tests\Command;

use Lordrhodos\GithubTools\Command\ConvertCommand;
use Lordrhodos\GithubTools\Converter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
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
        $inputMock
            ->method('getArgument')
            ->willReturnOnConsecutiveCalls('foo', 'bar');

        $outputMock = $this->createMock(ConsoleOutput::class);
        $outputMock
            ->method('getFormatter')
            ->willReturn(new OutputFormatter());

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
        $exitCode = $command->execute($inputMock, $outputMock);

        $this->assertSame(0, (int) $exitCode);
    }

    public function testExecuteWithAuthenticateOption(): void
    {
        $inputMock = $this->createMock(StringInput::class);
        $inputMock
            ->method('getArgument')
            ->willReturnOnConsecutiveCalls('foo', 'bar');
        $inputMock
            ->expects($this->once())
            ->method('hasOption')
            ->with('authenticate')
            ->willReturn(true);

        $outputMock = $this->createMock(ConsoleOutput::class);
        $outputMock
            ->method('getFormatter')
            ->willReturn(new OutputFormatter());

        $converterMock = $this->createMock(Converter::class);
        $converterMock->method('convert')
                      ->with('foo', 'bar')
                      ->willReturn('Changelog for foo/bar');
        $converterMock
            ->expects($this->once())
            ->method('setToken')
            ->with('token');

        $command = (new class($converterMock) extends ConvertCommand {
              public function execute(InputInterface $input, OutputInterface $output): void
              {
                  parent::execute($input, $output);
              }
        });
        $questionHelperMock = $this->createMock(QuestionHelper::class);
        $questionHelperMock
            ->expects($this->once())
            ->method('ask')
            ->willReturnOnConsecutiveCalls('token');

        $command->setHelperSet(new HelperSet(['question' => $questionHelperMock]));
        $exitCode = $command->execute($inputMock, $outputMock);

        $this->assertSame(0, (int) $exitCode);
    }

    public function testExecuteWithAuthenticateOptionAndInvalidTokenInput(): void
    {
        $inputMock = $this->createMock(StringInput::class);
        $inputMock
            ->method('getArgument')
            ->willReturnOnConsecutiveCalls('foo', 'bar');
        $inputMock
            ->expects($this->once())
            ->method('hasOption')
            ->with('authenticate')
            ->willReturn(true);

        $outputMock = $this->createMock(ConsoleOutput::class);
        $outputMock
            ->method('getFormatter')
            ->willReturn(new OutputFormatter());

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
        $questionHelperMock = $this->createMock(QuestionHelper::class);
        $questionHelperMock
            ->expects($this->exactly(3))
            ->method('ask')
            ->willReturn('');

        $command->setHelperSet(new HelperSet(['question' => $questionHelperMock]));

        $this->expectException(\RuntimeException::class);
        $exitCode = $command->execute($inputMock, $outputMock);
    }
}
