<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\Type\ExerciseType;
use App\Repository\ExerciseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Exercise;

class ExerciseController extends AbstractController
{


    #[Route('/exercises', name: 'show-exercises', methods: ['GET'])]
    public function index(ExerciseRepository $exerciseRepository): Response
    {
        $exercises = $exerciseRepository->findAll();
        return $this->render('exercise/showExercisesPage.html.twig', [
            'controller_name' => 'ExerciseController',
            'exercises' => $exercises,
        ]);
    }

    #[Route('/exercises/{id}', name: 'get-exercise', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, ExerciseRepository $exerciseRepository): Response
    {
        $exercise = $exerciseRepository->find($id);
        if (!$exercise) {
            throw $this->createNotFoundException('No exercise found for id ' . $id);
        }
        return $this->render('exercise/showExercise.html.twig', [
            'exercise' => $exercise,
        ]);
    }

    #[Route('/exercises/new', name: 'new-exercise', methods: ['GET'])]
    public function new(): Response
    {
        $exercise = new Exercise();
        $form = $this->createForm(ExerciseType::class, $exercise,[
            'action' => $this->generateUrl('create-exercise'),
            'method'=>'POST'
        ]);

        return $this->render('exercise/addExercisePage.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/exercises', name: 'create-exercise', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exercise = new Exercise();
        $form = $this->createForm(ExerciseType::class, $exercise);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exercise);
            $entityManager->flush();
            $this->addFlash('success', 'Exercise created successfully!');
            return $this->redirectToRoute('show-exercises');
        }

        return $this->render('exercise/addExercisePage.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/exercises/{id}/edit', name: 'edit-exercise', methods: ['GET'])]
    public function edit(int $id, ExerciseRepository $exerciseRepository): Response
    {
        $exercise = $exerciseRepository->find($id);
        if (!$exercise) {
            throw $this->createNotFoundException('No exercise found for id ' . $id);
        }

        $form = $this->createForm(ExerciseType::class, $exercise, [
            'action' => $this->generateUrl('update-exercise', ['id' => $exercise->getId()]),
            'method' => 'PATCH',
        ]);

        return $this->render('exercise/editExercise.html.twig', [
            'form' => $form,
            'exercise' => $exercise,
        ]);
    }

    #[Route('/exercises/{id}', name: 'update-exercise', methods: ['PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ExerciseRepository $exerciseRepository): Response
    {
        $exercise = $exerciseRepository->find($id);
        if (!$exercise) {
            throw $this->createNotFoundException('No exercise found for id ' . $id);
        }

        $form = $this->createForm(ExerciseType::class, $exercise, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Exercise updated successfully!');
            return $this->redirectToRoute('show-exercises');
        }

        return $this->render('exercise/editExercise.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/exercise/{id}', name: 'delete-exercise', methods: ['GET','DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager, ExerciseRepository $exerciseRepository): Response
    {
        $exercise=$exerciseRepository->findExerciseById($id);
        $entityManager->remove($exercise);
        $entityManager->flush();
        return $this->redirectToRoute('show-exercises');
    }



}
