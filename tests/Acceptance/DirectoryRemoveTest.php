<?php

namespace App\Tests\Acceptance;

use App\DataFixtures\UserReferenceTrait;
use App\DataFixtures\Users;
use Dontdrinkandroot\GitkiBundle\Service\FileSystem\FileSystemService;
use Dontdrinkandroot\GitkiBundle\Service\Wiki\WikiService;
use Dontdrinkandroot\Path\DirectoryPath;
use Dontdrinkandroot\Path\FilePath;

class DirectoryRemoveTest extends BaseAcceptanceTest
{
    use UserReferenceTrait;

    /**
     * {@inheritdoc}
     */
    protected function getFixtureClasses()
    {
        return [Users::class];
    }

    public function testRemoveEmptyDirectoryTest()
    {
        $directoryPath = DirectoryPath::parse('/testdirectory/');
        /** @var FileSystemService $fileSystemService */
        $fileSystemService = $this->getContainer()->get(
            'test.Dontdrinkandroot\GitkiBundle\Service\FileSystem\FileSystemService'
        );
        /** @var WikiService $wikiService */
        $wikiService = $this->getContainer()->get('test.Dontdrinkandroot\GitkiBundle\Service\Wiki\WikiService');
        $wikiService->createFolder($directoryPath);
        $this->assertFileExists(
            $directoryPath->prepend($fileSystemService->getBasePath())->toAbsoluteFileSystemString()
        );

        $this->login($this->getUser(Users::COMMITTER));
        $this->client->request('GET', '/browse/testdirectory/?action=remove');
        $this->assertStatusCode(302, $this->client);
        $this->assertEquals('/browse/', $this->client->getResponse()->headers->get('Location'));

        $this->assertFileNotExists(
            $directoryPath->prepend($fileSystemService->getBasePath())->toAbsoluteFileSystemString()
        );
    }

    public function testRemoveNonEmptyDirectoryTest()
    {
        /** @var FileSystemService $fileSystemService */
        $fileSystemService = $this->getContainer()->get(
            'test.Dontdrinkandroot\GitkiBundle\Service\FileSystem\FileSystemService'
        );

        $exampleFile = FilePath::parse('/examples/toc-example.md');
        $exampleDirectory = DirectoryPath::parse('/examples/');

        $this->assertFileExists(
            $exampleFile->prepend($fileSystemService->getBasePath())->toAbsoluteFileSystemString()
        );
        $this->assertFileExists(
            $exampleDirectory->prepend($fileSystemService->getBasePath())->toAbsoluteFileSystemString()
        );

        $this->logIn($this->getUser(Users::COMMITTER));
        $crawler = $this->client->request('GET', '/browse/examples/?action=remove');
        $this->assertStatusCode(200, $this->client);
        $submitButton = $crawler->selectButton('Remove all files');
        $form = $submitButton->form();
        $this->client->submit(
            $form,
            [
                'form[commitMessage]' => 'A test commit message'
            ]
        );
        $this->assertStatusCode(302, $this->client);
        $this->assertEquals('/browse/', $this->client->getResponse()->headers->get('Location'));

        $this->assertFileNotExists(
            $exampleFile->prepend($fileSystemService->getBasePath())->toAbsoluteFileSystemString()
        );
        $this->assertFileNotExists(
            $exampleDirectory->prepend($fileSystemService->getBasePath())->toAbsoluteFileSystemString()
        );
    }

    public function testRemoveNonEmptyDirectoryWithLockTest()
    {
        $exampleFile = FilePath::parse('/examples/toc-example.md');
        $exampleDirectory = DirectoryPath::parse('/examples/');

        /** @var FileSystemService $fileSystemService */
        $fileSystemService = $this->getContainer()->get(
            'test.Dontdrinkandroot\GitkiBundle\Service\FileSystem\FileSystemService'
        );
        /** @var WikiService $wikiService */
        $wikiService = $this->getContainer()->get('test.Dontdrinkandroot\GitkiBundle\Service\Wiki\WikiService');
        $wikiService->createLock($this->getUser(Users::ADMIN), $exampleFile);

        $this->assertFileExists(
            $exampleFile->prepend($fileSystemService->getBasePath())->toAbsoluteFileSystemString()
        );
        $this->assertFileExists(
            $exampleDirectory->prepend($fileSystemService->getBasePath())->toAbsoluteFileSystemString()
        );

        $this->logIn($this->getUser(Users::COMMITTER));
        $crawler = $this->client->request('GET', '/browse/examples/?action=remove');
        $this->assertStatusCode(200, $this->client);
        $submitButton = $crawler->selectButton('Remove all files');
        $form = $submitButton->form();
        $this->client->submit(
            $form,
            [
                'form[commitMessage]' => 'A test commit message'
            ]
        );
        $this->assertStatusCode(500, $this->client);
    }
}
