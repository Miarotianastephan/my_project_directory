<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Action de connexion de l'utilisateur.
     *
     * Cette méthode gère l'affichage de la page de connexion. Si un utilisateur est déjà connecté,
     * il est redirigé vers la page d'administration. Si une tentative de connexion a échoué,
     * un message d'erreur est affiché.
     *
     * @param Request $request La requête HTTP contenant les informations de la session actuelle.
     * @param AuthenticationUtils $authUtils Utilitaire de sécurité permettant d'obtenir les erreurs d'authentification et le dernier nom d'utilisateur.
     *
     * @return Response La réponse HTTP qui contient la vue de la page de connexion.
     */
    #[Route(path: '/', name: 'user_login')]
    public function loginUser(Request $request, AuthenticationUtils $authUtils): Response
    {
        if ($this->security->getUser()) {
            return $this->redirectToRoute('admin_users');
        }
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        $message = $request->query->get('message');

        return $this->render('back_office/index.html.twig', [
            'error' => $error,
            'lastUsername' => $lastUsername,
            'message' => $message
        ]);
    }

    /**
     * Action de déconnexion de l'utilisateur.
     *
     * Cette méthode est appelée lors de la déconnexion de l'utilisateur.
     * Symfony gère automatiquement la logique de déconnexion lorsque cette méthode est exécutée.
     *
     * @return void
     */
    #[Route(path: '/logout', name: 'user_logout', methods: ['GET'])]
    public function logoutUser()
    {
    }
}
