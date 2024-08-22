<?php

namespace App\Controller;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/ux')]
class UxController extends AbstractController
{
    #[Route('/', name: 'ux_boutton', methods: ['GET'])]
    public function index(TestRepository $testRepository): Response
    {
        return $this->render('ux/index.html.twig');
    }
    #[Route('/popup', name: 'popup', methods: ['GET'])]
    public function popupPage()
    {
        return $this->render('ux/popup_page.html.twig');
    }
}