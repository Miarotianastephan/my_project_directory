<?php

namespace App\Security;

use App\Entity\Utilisateur;
use App\Exception\InvalidUserStatusException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

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
        dump([
            'Requete'=> '1)',
            '_route' => $request->attributes->get('_route'),
            'method' => $request->getMethod()
        ]);
        return $request->attributes->get('_route') === 'user_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $user_matricule = $request->request->get('user_matricule');
        $user_password = $request->request->get('user_pass');
        // dump([
        //     'Requete' => '2)',
        //     'user_matricule' => $user_matricule,
        //     'user_password' => $user_password,
        // ]);

        $this->isValidUser($user_matricule);

        return new Passport(
            new UserBadge($user_matricule),
            new CustomCredentials(
                function($credentials, Utilisateur $user) {
                    return $this->adService->authenticate($user->getUserMatricule(), $credentials);
                },
                $user_password
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('admin_users'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($exception instanceof InvalidUserStatusException) {
            $errorMessage = $exception->getMessageKey();
        } elseif ($exception instanceof BadCredentialsException) {
            $errorMessage = 'Matricule ou Mot de passe incorrect.';
        } else {
            $errorMessage = 'Erreur d\'authentification.';
        }
        $url =  $this->router->generate('user_login', ['message' => $errorMessage]);
        return new RedirectResponse($url);
    }

    public function isValidUser($user_matricule){
        // Find in database
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['user_matricule' => $user_matricule]);
        // User is not found in database
        if ($user === null) {
            dump(['user' => $user]);
            $exception = new InvalidUserStatusException($user_matricule);
            throw $exception;
        }
    }

}