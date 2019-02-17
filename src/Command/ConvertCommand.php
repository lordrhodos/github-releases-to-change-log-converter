<?php declare(strict_types=1);

namespace Lordrhodos\GithubTools\Command;

use Lordrhodos\GithubTools\Converter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConvertCommand extends Command
{
    /**
     * @var Converter
     */
    private $converter;

    public function __construct(Converter $converter, string $name = null)
    {
        parent::__construct($name);
        $this->converter = $converter;
    }

    protected function configure(): void
    {
        $this->setName('github:convert-releases');
        $this->setDescription('convert releases into a CHANGELOG.md file');
        $this->addArgument('owner', InputArgument::REQUIRED, 'github owner');
        $this->addArgument('repository', InputArgument::REQUIRED, 'github repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $owner = $input->getArgument('owner');
        $repository = $input->getArgument('repository');

        $io = new SymfonyStyle($input, $output);
        $io->title(\sprintf('Creating Changelog from releases for https://github.com/%s/%s', $owner, $repository));

        $changelog = $this->converter->convert($owner, $repository);
        $io->text($changelog);
    }
}
