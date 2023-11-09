<?php

namespace App\Controller;

use App\Contracts\CacheInterface;
use App\Entity\User;
use App\Service\CountryListService;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('{_locale<%app.supported.locales%>}')]
class ProfileController extends AbstractController
{
    /**
     * @throws GuzzleException
     * @throws \JsonException
     * @throws InvalidArgumentException
     */
    #[Route('/profile', name: 'app_profile')]
    public function index(
        CountryListService          $timeZone,
        Request                     $request,
        CacheInterface              $cache,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $cacheItem = $cache->cache;
        $countryList = $cacheItem->getItem('country_list');
        if (!$countryList->isHit()) {
            $countryList->set($timeZone->getCountryList());
            $countryList->expiresAfter(3600);
            $cacheItem->save($countryList);
        }

        $loginUser = $this->getUser();
        if ($this->isCsrfTokenValid('update_user', $request->request->get('_csrf_token'))) {
            $user = $entityManager->getRepository(User::class)->find($loginUser);
            if ($user !== null) {
                $user->setEmail($request->request->get('email'))
                    ->setTimeZone($request->request->get('selectedTimezone'));
                if ($request->request->get('password') !== '') {
                    $user->setPassword($userPasswordHasher->hashPassword(
                        $user,
                        $request->request->get('password'))
                    );
                }
                $entityManager->flush();
            }
        }

        return $this->render('profile/index.html.twig', [
            'user' => $loginUser,
            'countryList' => $countryList->get(),
        ]);
    }
}
