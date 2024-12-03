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
     * Page de connexion
     *
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return Response
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
     * Déconnexion
     *
     * @return void
     */
    #[Route(path: '/logout', name: 'user_logout', methods: ['GET'])]
    public function logoutUser()
    {
    }
}
