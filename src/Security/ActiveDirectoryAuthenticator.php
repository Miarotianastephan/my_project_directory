<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class ActiveDirectoryAuthenticator extends AbstractAuthenticator
{
    private $entityManager;
    private $router;
    private $adService;

    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, ActiveDirectoryService $adService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->adService = $adService;
    }

    public function supports(Request $request): ?bool
    {
        $routeRequest = ['Mapping function'=> 'supports()','_route' => $request->attributes->get('_route'), 'method' => $request->getMethod()];
        dump($routeRequest);

        return $request->attributes->get('_route') === 'user_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('user_matricule');
        $password = $request->request->get('user_pass');

        dump("FROM AUTHENTICATOR". " " . $email. " " . $password);

        return new Passport(
            new UserBadge($email, function($userIdentifier) {
                return $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['user_matricule' => $userIdentifier]);
            }),
            new CustomCredentials(
                function($credentials, Utilisateur $user) {
                    return $this->adService->authenticate($user->getUserMatricule(), $credentials);
                },
                $password
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('admin_users'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}