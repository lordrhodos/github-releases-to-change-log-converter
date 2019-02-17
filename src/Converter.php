<?php declare(strict_types=1);

namespace Lordrhodos\GithubTools;

use Github\Client;

class Converter
{
    /**
     * @var Client
     */
    private $githubClient;

    public function __construct(Client $githubClient)
    {
        $this->githubClient = $githubClient;
    }

    public function convert(string $owner, string $repository): string
    {
        $releases = $this->getReleases($owner, $repository);
        $changelog = new Changelog('Changelog');
        foreach ($releases as $release)
        {
            $release = Release::createFromArray((array) $release);
            $changelog->addRelease($release);
        }

        return (string) $changelog;
    }

    public function getReleases(string $owner, string $repository): array
    {
        return $this->githubClient->api('repos')->releases()->all($owner, $repository);
    }
}
