<?php

namespace App\Controller;

use App\Repository\ExerciceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exercice')]
class ExerciceController extends AbstractController
{
    #[Route('/', name: 'app_exercice')]
    public function index(ExerciceRepository $exerciceRepository): Response
    {
        $date = new \DateTime();

        return $this->render('exercice/index.html.twig',
            ['exercices' => $exerciceRepository->getExerciceValide($date)]
        );
    }
}
