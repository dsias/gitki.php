<?php


namespace Dontdrinkandroot\Gitki\WebBundle\Tests\Acceptance;

use Dontdrinkandroot\Gitki\WebBundle\DataFixtures\ORM\UserReferenceTrait;
use Dontdrinkandroot\Gitki\WebBundle\DataFixtures\ORM\Users;
use Dontdrinkandroot\Gitki\WebBundle\Entity\User;

class AccessRightsTest extends BaseAcceptanceTest
{

    use UserReferenceTrait;

    /**
     * {@inheritdoc}
     */
    protected function getFixtureClasses()
    {
        return [Users::class];
    }

    public function testAnonymousRights()
    {
        $this->assertAccessRights('/history');

        $this->assertAccessRights('/browse/');
        $this->assertAccessRights('/browse/?action=list');
        $this->assertAccessRights('/browse/?action=file.upload');
        $this->assertAccessRights('/browse/?action=file.create&extension=txt');
        $this->assertAccessRights('/browse/?action=file.create&extension=md');
        $this->assertAccessRights('/browse/?action=subdirectory.create');

        $this->assertAccessRights('/browse/index.md');
        $this->assertAccessRights('/browse/index.md?action=history');
        $this->assertAccessRights('/browse/index.md?action=edit');
        $this->assertAccessRights('/browse/index.md?action=move');
        $this->assertAccessRights('/browse/index.md?action=delete');
    }

    public function testWatcherRights()
    {
        $this->assertAccessRights('/history', 200, $this->getUser(Users::WATCHER));

        $this->assertAccessRights('/browse/', 302, $this->getUser(Users::WATCHER));
        $this->assertAccessRights('/browse/?action=list', 200, $this->getUser(Users::WATCHER));
        $this->assertAccessRights('/browse/?action=file.upload', null, $this->getUser(Users::WATCHER));
        $this->assertAccessRights('/browse/?action=file.create&extension=txt', null, $this->getUser(Users::WATCHER));
        $this->assertAccessRights('/browse/?action=file.create&extension=md', null, $this->getUser(Users::WATCHER));
        $this->assertAccessRights('/browse/?action=subdirectory.create', null, $this->getUser(Users::WATCHER));

        $this->assertAccessRights('/browse/index.md', 200, $this->getUser(Users::WATCHER));
        $this->assertAccessRights('/browse/index.md?action=history', 200, $this->getUser(Users::WATCHER));
        $this->assertAccessRights('/browse/index.md?action=edit', null, $this->getUser(Users::WATCHER));
        $this->assertAccessRights('/browse/index.md?action=move', null, $this->getUser(Users::WATCHER));
        $this->assertAccessRights('/browse/index.md?action=delete', null, $this->getUser(Users::WATCHER));
    }

    public function testCommitterRights()
    {
        $this->assertAccessRights('/history', 200, $this->getUser(Users::COMMITTER));

        $this->assertAccessRights('/browse/', 302, $this->getUser(Users::COMMITTER));
        $this->assertAccessRights('/browse/?action=list', 200, $this->getUser(Users::COMMITTER));
        $this->assertAccessRights('/browse/?action=file.upload', 200, $this->getUser(Users::COMMITTER));
        $this->assertAccessRights('/browse/?action=file.create&extension=txt', 200, $this->getUser(Users::COMMITTER));
        $this->assertAccessRights('/browse/?action=file.create&extension=md', 200, $this->getUser(Users::COMMITTER));
        $this->assertAccessRights('/browse/?action=subdirectory.create', 200, $this->getUser(Users::COMMITTER));

        $this->assertAccessRights('/browse/index.md', 200, $this->getUser(Users::COMMITTER));
        $this->assertAccessRights('/browse/index.md?action=history', 200, $this->getUser(Users::COMMITTER));
        $this->assertAccessRights('/browse/index.md?action=edit', 200, $this->getUser(Users::COMMITTER));
        $this->assertAccessRights('/browse/index.md?action=move', 200, $this->getUser(Users::COMMITTER));
        $this->assertAccessRights('/browse/index.md?action=delete', 302, $this->getUser(Users::COMMITTER));
    }

    public function testAdminRights()
    {
        $this->assertAccessRights('/history', 200, $this->getUser(Users::ADMIN));

        $this->assertAccessRights('/browse/', 302, $this->getUser(Users::ADMIN));
        $this->assertAccessRights('/browse/?action=list', 200, $this->getUser(Users::ADMIN));
        $this->assertAccessRights('/browse/?action=file.upload', 200, $this->getUser(Users::ADMIN));
        $this->assertAccessRights('/browse/?action=file.create&extension=txt', 200, $this->getUser(Users::ADMIN));
        $this->assertAccessRights('/browse/?action=file.create&extension=md', 200, $this->getUser(Users::ADMIN));
        $this->assertAccessRights('/browse/?action=subdirectory.create', 200, $this->getUser(Users::ADMIN));

        $this->assertAccessRights('/browse/index.md', 200, $this->getUser(Users::ADMIN));
        $this->assertAccessRights('/browse/index.md?action=history', 200, $this->getUser(Users::ADMIN));
        $this->assertAccessRights('/browse/index.md?action=edit', 200, $this->getUser(Users::ADMIN));
        $this->assertAccessRights('/browse/index.md?action=move', 200, $this->getUser(Users::ADMIN));
        $this->assertAccessRights('/browse/index.md?action=delete', 302, $this->getUser(Users::ADMIN));
    }

    /**
     * @param string $url            The url to test.
     * @param null   $expectedStatus The expected status code. Null if login is expected.
     * @param User   $user           The user to test or null for anonymous.
     */
    protected function assertAccessRights($url, $expectedStatus = null, User $user = null)
    {
        $this->logOut();
        if (null !== $user) {
            $this->logIn($user);
        }
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        if (null === $expectedStatus) {
            $this->assertEquals(302, $response->getStatusCode(), sprintf('%s: Login expected', $url));
            $this->assertEquals('http://localhost/login/', $response->headers->get('Location'));

            return;
        }

        $this->assertEquals(
            $expectedStatus,
            $response->getStatusCode(),
            sprintf('%s [%s]', $url, $user !== null ? $user->getUsername() : null)
        );
    }
}
