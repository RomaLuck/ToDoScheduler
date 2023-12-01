<?php

namespace App\Security;

use App\Entity\User;
use App\Event\RegistrationEvent;
use App\EventSubscriber\RegistrationSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class MyGoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $router;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EventDispatcherInterface $dispatcher;
    private MailerInterface $mailer;

    public function __construct(
        EventDispatcherInterface    $dispatcher,
        MailerInterface             $mailer,
        ClientRegistry              $clientRegistry,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        UrlGeneratorInterface             $router)
    {
        $this->dispatcher = $dispatcher;
        $this->mailer = $mailer;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $email = $googleUser->getEmail();

                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy([
                    'client_id' => $googleUser->getId(),
                    'email' => $email
                ]);

                if ($existingUser) {
                    return $existingUser;
                }

                $user = new User();
                $user->setClientId($googleUser->getId())
                    ->setEmail($email)
                    ->setAcceptedTerms(false)
                    ->setPassword(
                        $this->userPasswordHasher->hashPassword(
                            $user,
                            $googleUser->getId()
                        )
                    );
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $event = new RegistrationEvent($email);
                $this->dispatcher->dispatch($event, RegistrationEvent::NAME);
                $this->dispatcher->addSubscriber(new RegistrationSubscriber($this->mailer, $this->router));

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
//        $targetUrl = $this->router->generate('app_task');
//
//        return new RedirectResponse($targetUrl);

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}