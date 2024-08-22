<?php

namespace App\Controller;

use App\Entity\Test;
use Psr\Log\LoggerInterface;
use App\Form\TestType;
use App\Repository\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/', name: 'app_test_index', methods: ['GET'])]
    public function index(TestRepository $testRepository): Response
    {
        return $this->render('test/index.html.twig', [
            'tests' => $testRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_test_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $test = new Test();
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la date du formulaire
            $date = $form->get('test_date')->getData();

            // Vérifier si la date est bien un objet DateTime
            if (!$date instanceof \DateTimeInterface) {
                $date = \DateTimeInterface::createFromFormat('Y-m-d H:i:s', $date);
                if ($date === false) {
                    throw new \Exception('Invalid date format');
                }
            }

            // Assigner la date convertie à l'entité
            $test->setTestDate($date);

            // Persister et sauvegarder l'entité
            $entityManager->persist($test);
            $entityManager->flush();

            return $this->redirectToRoute('app_test_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('test/new.html.twig', [
            'test' => $test,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_test_show', methods: ['GET'])]
    public function show(Test $test): Response
    {
        return $this->render('test/show.html.twig', [
            'test' => $test,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_test_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Test $test, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_test_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('test/edit.html.twig', [
            'test' => $test,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_test_delete', methods: ['POST'])]
    public function delete(Request $request, Test $test, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$test->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($test);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_test_index', [], Response::HTTP_SEE_OTHER);
    }
}
