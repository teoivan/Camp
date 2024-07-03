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


    #[Route('/exercises', name: 'exercises')]
    public function index(ExerciseRepository $exerciseRepository): Response
    {
        $exercises = $exerciseRepository->findAll();
        return $this->render('exercise/showExercisesPage.html.twig', [
            'controller_name' => 'ExerciseController',
            'exercises' => $exercises,

        ]);
    }

    #[Route('/add-exercise', name: 'add_exercise',methods: array('GET', 'POST'))]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exercise = new Exercise();

        $form = $this->createForm(ExerciseType::class, $exercise);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $exercise = $form->getData();
            $entityManager->persist($exercise);
            $entityManager->flush();
            $this->addFlash('success', 'Exercise created successfully!');
            return $this->redirectToRoute('add_exercise');

        }

        return $this->render('exercise/addExercisePage.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/exercise/{id}', name: 'edit-exercise', methods: ['GET','PATCH'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, ExerciseRepository $exerciseRepository): Response
    {
        $exercise=$exerciseRepository->findExerciseById($id);
        $form = $this->createForm(ExerciseType::class, $exercise,[
            'action' => $this->generateUrl('edit-exercise', ['id'=>$id]),
            'method' => 'PATCH',]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $exercise = $form->getData();
            $entityManager->persist($exercise);
            $entityManager->flush();
            $this->addFlash('success', 'Exercise Edited!');
            return $this->redirectToRoute('edit-exercise', [
                'id' => $id,
            ]);
        }
        return $this->render('exercise/editExercise.html.twig', [
            'form' => $form,
        ]);
    }


}
