<?php

namespace App\Security;

use App\Constant\Roles;
use App\Entity\Utilisateur;
use App\Exception\InvalidDataUserException;
use App\Exception\InvalidUserStatusException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

/**
 * Authenticator pour la gestion de l'authentification via Active Directory.
 *
 * Cette classe intègre le système de sécurité de Symfony pour gérer l'authentification
 * des utilisateurs via un serveur LDAP (Active Directory).
 */
class ActiveDirectoryAuthenticator extends AbstractAuthenticator
{
    private $entityManager;
    private $router;
    private $adService;


    /**
     * Constructeur.
     *
     * @param EntityManagerInterface $entityManager L'EntityManager pour interagir avec la base de données.
     * @param RouterInterface $router Le routeur Symfony pour gérer les redirections.
     * @param ActiveDirectoryService $adService Le service d'authentification Active Directory.
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, ActiveDirectoryService $adService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->adService = $adService;
    }

    /**
     * Vérifie si cette authenticator doit gérer la requête actuelle.
     *
     * @param Request $request La requête HTTP.
     *
     * @return bool|null Retourne `true` si la requête est gérée, `false` sinon.
     */
    public function supports(Request $request): ?bool
    {
        /*dump([
            '_route' => $request->attributes->get('_route'),
            'method' => $request->getMethod()
        ]);*/
        return $request->attributes->get('_route') === 'user_login' && $request->isMethod('POST');
    }

    /**
     * Gère le processus d'authentification.
     *
     * @param Request $request La requête contenant les données d'authentification.
     *
     * @return Passport Retourne un objet Passport pour valider l'utilisateur.
     *
     * @throws InvalidDataUserException Si les données de l'utilisateur sont invalides.
     */
    public function authenticate(Request $request): Passport
    {
        // Replace
        $usrMatricule = $request->request->get('user_matricule');
        $user_matricule = $usrMatricule;
        $user_password = $request->request->get('user_pass');
        // vérifier si non vide
        if (strlen($user_matricule) == 0 || strlen($user_password) == 0) {
            throw new InvalidDataUserException();
        }
        // enlever les espaces
        $user_matricule = trim($user_matricule);
        $user_password = trim($user_password);

        // Pour vérifier sio l'utilisateur est dans la base de donnée
        $this->isValidUser($user_matricule);

        return new Passport(
            new UserBadge($user_matricule),
            new CustomCredentials(
                function ($credentials, Utilisateur $user) {
                    return $this->adService->authenticate($user->getUserMatricule(), $credentials);
                },
                $user_password
            )
        );
    }

    /**
     * Vérifie si l'utilisateur existe en base de données.
     *
     * @param string $user_matricule Le matricule de l'utilisateur.
     *
     * @throws InvalidUserStatusException Si l'utilisateur n'est pas trouvé.
     */
    public function isValidUser($user_matricule)
    {
        // Find in database
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['user_matricule' => $user_matricule]);
        // User is not found in database
        if ($user === null) {
            dump(['user' => $user]);
            $exception = new InvalidUserStatusException($user_matricule);
            throw $exception;
        }
    }

    /**
     * Gestion des redirections en cas de succès de l'authentification.
     *
     * @param Request $request La requête d'authentification.
     * @param TokenInterface $token Le jeton de l'utilisateur authentifié.
     * @param string $firewallName Le nom du firewall.
     *
     * @return Response|null Une redirection vers une route spécifique en fonction du rôle de l'utilisateur.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // mila gestion des redirections selon ny rôle
        $user = $token->getUser();
        $user_roles = $user->getRoles();
        if (in_array(Roles::ADMIN, $user_roles)) {
            return new RedirectResponse($this->router->generate('admin_users'));
        } else if (in_array(Roles::DEMANDEUR, $user_roles)) {
            return new RedirectResponse($this->router->generate('demandeur.liste_demande'));
        } else if (in_array(Roles::SG, $user_roles)) {
            return new RedirectResponse($this->router->generate('SG.liste_demande_en_attente'));
        } else if (in_array(Roles::TRESORIER, $user_roles)) {
            return new RedirectResponse($this->router->generate('tresorier.liste_demande_en_attente'));
        } else if (in_array(Roles::COMPTABLE, $user_roles)) {
            return new RedirectResponse($this->router->generate('app_tableau_depense_annuelle'));
        } else if (in_array(Roles::COMMISSAIRE_COMPTE, $user_roles)) {
            return new RedirectResponse($this->router->generate('app_commisaire_compte'));
        }
        return null;
    }

    /**
     * Gestion des erreurs en cas d'échec de l'authentification.
     *
     * @param Request $request La requête d'authentification.
     * @param AuthenticationException $exception L'exception levée.
     *
     * @return Response|null Une redirection vers la page de connexion avec un message d'erreur.
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($exception instanceof InvalidUserStatusException) {
            $errorMessage = $exception->getMessageKey();
        } elseif ($exception instanceof BadCredentialsException) {
            $errorMessage = 'Matricule ou Mot de passe incorrect.';
        } elseif ($exception instanceof InvalidDataUserException) {
            $errorMessage = $exception->getMessageKey();
        } else {
            $errorMessage = 'Erreur d\'authentification.';
        }
        $url = $this->router->generate('user_login', ['message' => $errorMessage]);
        return new RedirectResponse($url);
    }

}