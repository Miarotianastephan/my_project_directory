<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LuckyController extends AbstractController
{

    #[Route('/lucky/number')]
    public function number(): Response
    {
        $number = random_int(0, 100);
        return $this->redirectToRoute('hello_world');
    }

    #[Route('/lucky/hello', name:'hello_world')]
    public function hello_world(): Response
    {
        $number = random_int(0, 100);
        $helloText = 'Hello World User no°: '.$number;
        return $this->render('lucky/hello.html.twig', [
            'hello_text' => $helloText,
        ]);
    }
}