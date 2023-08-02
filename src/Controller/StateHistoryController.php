<?php

namespace App\Controller;

use App\Entity\StateHistory;
use App\Form\StateHistory1Type;
use App\Repository\StateHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/state/history')]
class StateHistoryController extends AbstractController
{
    #[Route('/', name: 'app_state_history_index', methods: ['GET'])]
    public function index(StateHistoryRepository $stateHistoryRepository): Response
    {
        return $this->render('state_history/index.html.twig', [
            'state_histories' => $stateHistoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_state_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stateHistory = new StateHistory();
        $form = $this->createForm(StateHistory1Type::class, $stateHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stateHistory);
            $entityManager->flush();

            return $this->redirectToRoute('app_state_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('state_history/new.html.twig', [
            'state_history' => $stateHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_state_history_show', methods: ['GET'])]
    public function show(StateHistory $stateHistory): Response
    {
        return $this->render('state_history/show.html.twig', [
            'state_history' => $stateHistory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_state_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StateHistory $stateHistory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StateHistory1Type::class, $stateHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_state_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('state_history/edit.html.twig', [
            'state_history' => $stateHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_state_history_delete', methods: ['POST'])]
    public function delete(Request $request, StateHistory $stateHistory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stateHistory->getId(), (string)$request->request->get('_token'))) {
            $entityManager->remove($stateHistory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_state_history_index', [], Response::HTTP_SEE_OTHER);
    }
}
