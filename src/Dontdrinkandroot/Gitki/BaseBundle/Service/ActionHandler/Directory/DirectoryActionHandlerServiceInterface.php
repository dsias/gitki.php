<?php

namespace Dontdrinkandroot\Gitki\BaseBundle\Service\ActionHandler\Directory;

use Dontdrinkandroot\Gitki\BaseBundle\Entity\User;
use Dontdrinkandroot\Path\DirectoryPath;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface DirectoryActionHandlerServiceInterface
{

    /**
     * @param DirectoryPath $directoryPath
     * @param Request       $request
     * @param User          $user
     *
     * @return Response
     */
    public function handle(DirectoryPath $directoryPath, Request $request, User $user);

    /**
     * @param DirectoryActionHandlerInterface $directoryActionHandler
     * @param string                          $action
     */
    public function registerHandler(DirectoryActionHandlerInterface $directoryActionHandler, $action);
}