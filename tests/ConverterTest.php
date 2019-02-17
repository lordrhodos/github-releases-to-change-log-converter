<?php declare(strict_types=1);

namespace Lordrhodos\GithubTools\Tests;

use Github\Client as GithubClient;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use Lordrhodos\GithubTools\Converter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ConverterTest extends TestCase
{
    /**
     * @covers \Lordrhodos\GithubTools\Converter::__construct
     */
    public function testCreateConverter(): void
    {
        $githubClientMock = $this->createMock(GithubClient::class);
        $converter = new Converter($githubClientMock);

        $converterReflection = new \ReflectionClass($converter);
        $githubClientProperty = $converterReflection->getProperty('githubClient');
        $githubClientProperty->setAccessible(true);

        $this->assertInstanceOf(Converter::class, $converter);
        $this->assertSame($githubClientMock, $githubClientProperty->getValue($converter));
    }

    /**
     * @covers \Lordrhodos\GithubTools\Converter::convert
     */
    public function testConvert(): void
    {
        $httpMock = new Client();
        $httpMock->setDefaultResponse($this->getSampleGithubReleasesResponse());
        $githubClient = GithubClient::createWithHttpClient($httpMock);
        $converter = new Converter($githubClient);

        $changelog = $converter->convert('foo', 'bar');
        $this->assertInstanceOf(Converter::class, $converter);
        $this->assertSame($githubClient, $githubClient);
        $this->assertIsString($changelog);
    }

    /**
     * @covers \Lordrhodos\GithubTools\Converter::getReleases
     */
    public function testGetReleases(): void
    {
        $httpMock = new Client();
        $httpMock->addResponse($this->getSampleGithubReleasesResponse());
        $githubClient = GithubClient::createWithHttpClient($httpMock);
        $converter = new Converter($githubClient);

        $releases = $converter->getReleases('foo', 'bar');
        $this->assertIsArray($releases);
    }

    /**
     * returns a sample response taken from the github releases api doc at
     * https://developer.github.com/v3/repos/releases/
     *
     * @return string
     */
    private function getSampleGithubReleasesResponse(): ResponseInterface
    {
        $body = '
        [
          {
            "url": "https://api.github.com/repos/octocat/Hello-World/releases/1",
            "html_url": "https://github.com/octocat/Hello-World/releases/v1.0.0",
            "assets_url": "https://api.github.com/repos/octocat/Hello-World/releases/1/assets",
            "upload_url": "https://uploads.github.com/repos/octocat/Hello-World/releases/1/assets{?name,label}",
            "tarball_url": "https://api.github.com/repos/octocat/Hello-World/tarball/v1.0.0",
            "zipball_url": "https://api.github.com/repos/octocat/Hello-World/zipball/v1.0.0",
            "id": 1,
            "node_id": "MDc6UmVsZWFzZTE=",
            "tag_name": "v1.0.0",
            "target_commitish": "master",
            "name": "v1.0.0",
            "body": "Description of the release",
            "draft": false,
            "prerelease": false,
            "created_at": "2013-02-27T19:35:32Z",
            "published_at": "2013-02-27T19:35:32Z",
            "author": {
              "login": "octocat",
              "id": 1,
              "node_id": "MDQ6VXNlcjE=",
              "avatar_url": "https://github.com/images/error/octocat_happy.gif",
              "gravatar_id": "",
              "url": "https://api.github.com/users/octocat",
              "html_url": "https://github.com/octocat",
              "followers_url": "https://api.github.com/users/octocat/followers",
              "following_url": "https://api.github.com/users/octocat/following{/other_user}",
              "gists_url": "https://api.github.com/users/octocat/gists{/gist_id}",
              "starred_url": "https://api.github.com/users/octocat/starred{/owner}{/repo}",
              "subscriptions_url": "https://api.github.com/users/octocat/subscriptions",
              "organizations_url": "https://api.github.com/users/octocat/orgs",
              "repos_url": "https://api.github.com/users/octocat/repos",
              "events_url": "https://api.github.com/users/octocat/events{/privacy}",
              "received_events_url": "https://api.github.com/users/octocat/received_events",
              "type": "User",
              "site_admin": false
            },
            "assets": [
              {
                "url": "https://api.github.com/repos/octocat/Hello-World/releases/assets/1",
                "browser_download_url": "https://github.com/octocat/Hello-World/releases/download/v1.0.0/example.zip",
                "id": 1,
                "node_id": "MDEyOlJlbGVhc2VBc3NldDE=",
                "name": "example.zip",
                "label": "short description",
                "state": "uploaded",
                "content_type": "application/zip",
                "size": 1024,
                "download_count": 42,
                "created_at": "2013-02-27T19:35:32Z",
                "updated_at": "2013-02-27T19:35:32Z",
                "uploader": {
                  "login": "octocat",
                  "id": 1,
                  "node_id": "MDQ6VXNlcjE=",
                  "avatar_url": "https://github.com/images/error/octocat_happy.gif",
                  "gravatar_id": "",
                  "url": "https://api.github.com/users/octocat",
                  "html_url": "https://github.com/octocat",
                  "followers_url": "https://api.github.com/users/octocat/followers",
                  "following_url": "https://api.github.com/users/octocat/following{/other_user}",
                  "gists_url": "https://api.github.com/users/octocat/gists{/gist_id}",
                  "starred_url": "https://api.github.com/users/octocat/starred{/owner}{/repo}",
                  "subscriptions_url": "https://api.github.com/users/octocat/subscriptions",
                  "organizations_url": "https://api.github.com/users/octocat/orgs",
                  "repos_url": "https://api.github.com/users/octocat/repos",
                  "events_url": "https://api.github.com/users/octocat/events{/privacy}",
                  "received_events_url": "https://api.github.com/users/octocat/received_events",
                  "type": "User",
                  "site_admin": false
                }
              }
            ]
          }
        ]
        ';

        $response = new Response(200, ['Content-Type' => 'application/json'], $body);

        return $response;
    }
}
