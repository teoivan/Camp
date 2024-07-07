<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Workout;
use App\Form\Type\UserType;
use App\Form\Type\WorkoutType;
use App\Repository\UserRepository;
use App\Repository\WorkoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WorkoutController extends AbstractController
{

    #[Route('/workouts', name: 'show-workouts')]
    public function index(WorkoutRepository $workoutRepository): Response
    {
        $workouts = $workoutRepository->findAll();
        return $this->render('workout/showWorkouts.html.twig', [
            'controller_name' => 'WorkoutController',
            'workouts' => $workouts,

        ]);
    }
    #[Route('/workout', name: 'add-workout', methods: ['POST', 'GET'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $workout = new Workout();

        $form = $this->createForm(WorkoutType::class, $workout);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $workout= $form->getData();
            $user=$userRepository->findUserById(1);
            $workout->setUser($user);
            $workout->setDate(new \DateTime());
            $entityManager->persist($workout);
            $entityManager->flush();
//            $this->addFlash('success', 'Workout added successfully!');
            return $this->redirectToRoute('show-workouts');
        }

        return $this->render('workout/addWorkout.html.twig', [
            'form' => $form,
        ]);

    }
}
