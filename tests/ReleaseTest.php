<?php declare(strict_types=1);

namespace Lordrhodos\GithubTools\Tests;

use Lordrhodos\GithubTools\Release;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ReleaseTest extends TestCase
{
    /**
     * @covers \Lordrhodos\GithubTools\Release::__construct
     * @covers \Lordrhodos\GithubTools\Release::getVersion
     * @covers \Lordrhodos\GithubTools\Release::getDate
     * @covers \Lordrhodos\GithubTools\Release::getBody
     */
    public function testCreation(): void
    {
        $release = new Release('1.0.0', '2019-01-01', 'first release');

        $this->assertSame('1.0.0', $release->getVersion());
        $this->assertSame('2019-01-01', $release->getDate());
        $this->assertSame('first release', $release->getBody());
    }

    /**
     * @covers \Lordrhodos\GithubTools\Release::createFromArray
     * @covers \Lordrhodos\GithubTools\Release::getVersion
     * @covers \Lordrhodos\GithubTools\Release::getDate
     * @covers \Lordrhodos\GithubTools\Release::getBody
     */
    public function testCreateFromArray(): void
    {
        $release = Release::createFromArray([
            'name' => '1.0.0',
            'published_at' => '2019-01-01',
            'body' => 'first release']
        );

        $this->assertInstanceOf(Release::class, $release);
        $this->assertSame('1.0.0', $release->getVersion());
        $this->assertSame('2019-01-01', $release->getDate());
        $this->assertSame('first release', $release->getBody());
    }


    /**
     * @covers \Lordrhodos\GithubTools\Release::createFromArray
     * @covers \Lordrhodos\GithubTools\Release::checkIfRequiredKeyIsPresent
     *
     * @dataProvider provideInvalidDataArrayForReleaseCreation
     */
    public function testCreateFromInvalidArray(array $data): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageForData($data);

        Release::createFromArray($data);
    }

    public function provideInvalidDataArrayForReleaseCreation(): array
    {
        return [
            [[]],
            [['name' => null]],
            [['name' => '']],
            [['name' => '1.0.0']],
            [['name' => '1.0.0', 'published_at' => null]],
            [['name' => '1.0.0', 'published_at' => '']],
            [['published_at' => null]],
            [['published_at' => '']],
            [['published_at' => '2019-01-01']],
            [['published_at' => '2019-01-01', 'name' => null]],
            [['published_at' => '2019-01-01', 'name' => '']],
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function expectExceptionMessageForData(array $data): void
    {
        if (empty($data['name'])) {
            $this->expectExceptionMessage('data is missing key [name]');
        } elseif (empty($data['published_at'])) {
            $this->expectExceptionMessage('data is missing key [published_at]');
        } elseif (empty($data['body'])) {
            $this->expectExceptionMessage('data is missing key [body]');
        }
    }
}
