<?php

namespace App\Controller;

use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{
    #[Route('/connect/facebook', name: 'connect_facebook_start')]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_task');
        }
        return $clientRegistry
            ->getClient('facebook_main')
            ->redirect([], [
                'public_profile', 'email'
            ]);
    }


    #[Route('/connect/facebook/check', name: 'connect_facebook_check')]
    public function connectCheckAction(UserRepository $repository): Response
    {
        $user = $repository->find($this->getUser());
        if ($user !== null && $user->isAcceptedTerms()) {
            return $this->redirectToRoute('app_task');
        }
        return $this->redirectToRoute('app_profile');
    }
}