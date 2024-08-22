<?php

namespace App\Controller;

use App\Entity\Test2;
use App\Form\Test2Type;
use App\Repository\Test2Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test2')]
class Test2Controller extends AbstractController
{
    #[Route('/', name: 'app_test2_index', methods: ['GET'])]
    public function index(Test2Repository $test2Repository): Response
    {
        return $this->render('test2/index.html.twig', [
            'test2s' => $test2Repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_test2_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $test2 = new Test2();
        $form = $this->createForm(Test2Type::class, $test2);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($test2);
            $entityManager->flush();

            return $this->redirectToRoute('app_test2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('test2/new.html.twig', [
            'test2' => $test2,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_test2_show', methods: ['GET'])]
    public function show(Test2 $test2): Response
    {
        return $this->render('test2/show.html.twig', [
            'test2' => $test2,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_test2_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Test2 $test2, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Test2Type::class, $test2);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_test2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('test2/edit.html.twig', [
            'test2' => $test2,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_test2_delete', methods: ['POST'])]
    public function delete(Request $request, Test2 $test2, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$test2->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($test2);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_test2_index', [], Response::HTTP_SEE_OTHER);
    }
}
