<?php

namespace App\Controller;

use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_task');
        }
        return $clientRegistry
            ->getClient('google')
            ->redirect([], [
                'profile', 'email'
            ]);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(UserRepository $repository): Response
    {
        $user = $repository->find($this->getUser());
        if ($user !== null && $user->isAcceptedTerms()) {
            return $this->redirectToRoute('app_task');
        }
        return $this->redirectToRoute('app_profile');
    }
}