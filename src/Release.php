<?php declare(strict_types=1);

namespace Lordrhodos\GithubTools;

use RuntimeException;

class Release
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $body;

    public function __construct(string $version, string $date, string $body = '')
    {
        $this->version = $version;
        $this->date = $date;
        $this->body = $body;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public static function createFromArray(array $data): self
    {
        self::checkIfRequiredKeyIsPresent($data, 'name');
        self::checkIfRequiredKeyIsPresent($data, 'published_at');

        return new static($data['name'], $data['published_at'], $data['body']);
    }

    private static function checkIfRequiredKeyIsPresent(array $data, string $key): void
    {
        if (empty($data[$key])) {
            throw new RuntimeException("data is missing key [$key]");
        }
    }
}
