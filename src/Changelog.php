<?php declare(strict_types=1);

namespace Lordrhodos\GithubTools;

class Changelog
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var Release[]
     */
    private $releases;

    /**
     * Changelog constructor.
     *
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
        $this->releases = [];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Release[]
     */
    public function getReleases(): array
    {
        return $this->releases;
    }

    public function addRelease(Release $release): void
    {
        $this->releases[] = $release;
    }

    public function __toString(): string
    {
        $markdown = $this->getFormattedTitle();
        $markdown .=  $this->getLineBreaks(2);
        foreach ($this->releases as $release) {
            $markdown .= $this->getReleaseEntry($release);
        }

        return $markdown;
    }

    protected function getReleaseEntry(Release $release): string
    {
        $releaseEntry = $this->getReleaseHeadline($release);
        $releaseEntry .= $this->getFormattedReleaseBody($release);;

        return $releaseEntry;
    }

    protected function getFormattedTitle(): string
    {
        return '# ' . $this->title;
    }

    protected function getLineBreaks(int $lineNumber = 1): string
    {
        $breaks = '';
        for ($i = 0; $i < $lineNumber; $i++) {
            $breaks .= PHP_EOL;
        }

        return $breaks;
    }

    protected function getReleaseHeadline(Release $release): string
    {
        $releaseDate = $this->getFormattedReleaseDate($release);
        $headline = sprintf('## [%s] - %s', $release->getVersion(), $releaseDate);
        $headline .= $this->getLineBreaks(2);

        return $headline;
    }

    protected function getFormattedReleaseDate(Release $release): string
    {
        $datetime = new \DateTime($release->getDate());
        $date = $datetime->format('Y-m-d');

        return $date;
    }

    /**
     * @param Release $release
     *
     * @return string
     */
    protected function getFormattedReleaseBody(Release $release): string
    {
        $body = $release->getBody();

        return $body ? $body . $this->getLineBreaks(2) : '';
    }


}
