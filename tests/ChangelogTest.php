<?php declare(strict_types=1);

namespace Lordrhodos\GithubTools\Tests;

use Lordrhodos\GithubTools\Changelog;
use Lordrhodos\GithubTools\Release;
use PHPUnit\Framework\TestCase;

class ChangelogTest extends TestCase
{
    /**
     * @covers \Lordrhodos\GithubTools\Changelog::__construct
     * @covers \Lordrhodos\GithubTools\Changelog::getTitle
     * @covers \Lordrhodos\GithubTools\Changelog::getReleases
     */
    public function testCreation(): void
    {
        $changelog = new Changelog('foo');
        $this->assertSame('foo', $changelog->getTitle());
        $this->assertSame([], $changelog->getReleases());
    }

    /**
     * @covers \Lordrhodos\GithubTools\Changelog::getReleases
     * @covers \Lordrhodos\GithubTools\Changelog::addRelease
     */
    public function testAddRelease(): void
    {
        $changelog = new Changelog('foo');
        $this->assertSame([], $changelog->getReleases());
        $release = $this->getNewRelease();
        $changelog->addRelease($release);
        $this->assertCount(1, $changelog->getReleases());
        $this->assertSame($release, $changelog->getReleases()[0]);
    }

    /**
     * @covers \Lordrhodos\GithubTools\Changelog::__toString
     * @covers \Lordrhodos\GithubTools\Changelog::getReleaseEntry
     */
    public function testToString(): void
    {
        $changelog = new Changelog('foo');
        $release = $this->getNewRelease();
        $changelog->addRelease($release);
        $markdown = (string) $changelog;
        $this->assertStringStartsWith('# foo', $markdown);
        $this->assertStringContainsString("## [1.0.0] - 2019-02-27\n\n", $markdown);
        $this->assertStringContainsString("first release\n", $markdown);
    }

    /**
     * @covers \Lordrhodos\GithubTools\Changelog::getFormattedTitle
     */
    public function testGetFormattedTitle(): void
    {
        $changelog = (new class('foo') extends Changelog {
            public function getFormattedTitle() : string{
                return parent::getFormattedTitle();
            }
        });
        $title = $changelog->getFormattedTitle();
        $this->assertSame('# foo', $title);
    }

    /**
     * @covers \Lordrhodos\GithubTools\Changelog::getReleaseHeadline
     */
    public function testGetReleaseHeadline()
    {
        $release = $this->getNewRelease();
        $changelog = (new class('foo') extends Changelog {
            public function getReleaseHeadline(Release $release): string
            {
                return parent::getReleaseHeadline($release);
            }
        });
        $headline = $changelog->getReleaseHeadline($release);
        $this->assertSame('## [1.0.0] - 2019-02-27' . PHP_EOL . PHP_EOL, $headline);
    }

    /**
     * @covers \Lordrhodos\GithubTools\Changelog::getFormattedReleaseDate
     */
    public function testGetFormattedReleaseDate(): void
    {
        $release = $this->getNewRelease();
        $changelog = (new class('foo') extends Changelog {
            public function getFormattedReleaseDate(Release $release): string
            {
                return parent::getFormattedReleaseDate($release);
            }

        });
        $date = $changelog->getFormattedReleaseDate($release);
        $this->assertSame('2019-02-27', $date);
    }

    /**
     * @covers \Lordrhodos\GithubTools\Changelog::getFormattedReleaseBody
     */
    public function testGetFormattedReleaseBody(): void
    {
        $release = $this->getNewRelease();
        $changelog = (new class('foo') extends Changelog {
            public function getFormattedReleaseBody(Release $release): string
            {
                return parent::getFormattedReleaseBody($release);
            }

        });
        $body = $changelog->getFormattedReleaseBody($release);
        $this->assertSame('first release' . PHP_EOL . PHP_EOL, $body);
    }

    /**
     * @covers \Lordrhodos\GithubTools\Changelog::getLineBreaks
     */
    public function testGetLineBreaks(): void
    {
        $release = $this->getNewRelease();
        $changelog = (new class('foo') extends Changelog {
            public function getLineBreaks(int $lineNumber = 1): string
            {
                return parent::getLineBreaks($lineNumber);
            }

        });
        $lineBreaks = $changelog->getLineBreaks();
        $this->assertSame(PHP_EOL, $lineBreaks);
        $lineBreaks = $changelog->getLineBreaks(1);
        $this->assertSame(PHP_EOL, $lineBreaks);
        $lineBreaks = $changelog->getLineBreaks(2);
        $this->assertSame(PHP_EOL . PHP_EOL, $lineBreaks);
    }

    /**
     * @return Release
     */
    private function getNewRelease(): Release
    {
        return new Release('1.0.0', '2019-02-27T19:35:32Z', 'first release');
    }
}
