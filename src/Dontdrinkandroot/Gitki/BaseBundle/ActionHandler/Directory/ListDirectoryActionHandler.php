<?php

namespace Dontdrinkandroot\Gitki\BaseBundle\ActionHandler\Directory;

use Dontdrinkandroot\Gitki\BaseBundle\Entity\User;
use Dontdrinkandroot\Gitki\BaseBundle\ActionHandler\AbstractContainerAwareHandler;
use Dontdrinkandroot\Gitki\BaseBundle\ActionHandler\Directory\DirectoryActionHandlerInterface;
use Dontdrinkandroot\Path\DirectoryPath;
use Symfony\Component\HttpFoundation\Request;

class ListDirectoryActionHandler extends AbstractContainerAwareHandler implements DirectoryActionHandlerInterface
{

    /**
     * {@inheritdoc}
     */
    public function handle(DirectoryPath $directoryPath, Request $request, User $user)
    {
        $directoryListing = $this->getWikiService()->listDirectory($directoryPath);

        return $this->render(
            'DdrGitkiBaseBundle:Wiki:directory.listing.html.twig',
            array(
                'path' => $directoryPath,
                'directoryListing' => $directoryListing
            )
        );
    }
}