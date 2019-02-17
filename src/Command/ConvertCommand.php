<?php declare(strict_types=1);

namespace Lordrhodos\GithubTools\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConvertCommand extends Command
{
    private const LIST_ACTION = 'list';
    private const CREATE_ACTION = 'create';
    private const ACTIONS = [
        self::LIST_ACTION,
        self::CREATE_ACTION,
    ];

    /**
     * @var ClientRepository
     */
    private $repository;

    public function __construct(ClientRepository $repository, string $name = null)
    {
        parent::__construct($name);
        $this->repository = $repository;
    }


    protected function configure()
    {
        $this->setName('oauth:client');
        $this->setDescription('manage oauth clients');
        $this->addArgument(
            'action',
            InputArgument::OPTIONAL,
            sprintf('the actions to perform (%s)', implode(',', self::ACTIONS)),
            self::LIST_ACTION
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        if (!\in_array($action, self::ACTIONS)) {
            throw new \RuntimeException('invalid action');
        }
        $io = new SymfonyStyle($input, $output);
        $io->title('OAuth2.0 Clients');

        switch ($action) {
            case self::CREATE_ACTION:
                $this->createClient($io);
                break;
            case self::LIST_ACTION:
            default:
                $this->listClients($io);
                break;
        }
    }

    private function listClients(SymfonyStyle $io): void
    {
        $io->section('Listing clients');
        $clients = $this->repository->findAll();

        $tableRows = [];
        /** @var Client $client */
        foreach ($clients as $client) {
            $tableRows[] = [
                $client->getId(),
                $client->getName(),
                $client->getRedirectUri(),
                $client->getHashedSecret(),
            ];
        }

        $tableHeader = ['id', 'name', 'redirectUri', 'hashedSecret'];
        $table = $io->table($tableHeader, $tableRows);
    }

    private function createClient(SymfonyStyle $io): void
    {
        $io->section('Creating a new client');

        $name = $io->askQuestion(new Question('Name'));
        $redirectUri = $io->askQuestion(new Question('Redirect Uri'));
        $secret = $io->askHidden('secret');

        $client = new Client();
        $client->setName($name);
        $client->setRedirectUri($redirectUri);
        $client->setSecret($secret);
        $this->repository->create($client);

        $io->success(sprintf('client with id "%s" created successfully!', $client->getId()));
    }
}
