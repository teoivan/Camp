<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Workout;
use App\Form\Type\EditWorkoutType;
use App\Form\Type\UserType;
use App\Form\Type\WorkoutType;
use App\Repository\ExerciseLogRepository;
use App\Repository\UserRepository;
use App\Repository\WorkoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WorkoutController extends AbstractController
{

    public function __construct(
        private Security $security,
    ){
    }

    #[Route('/workouts', name: 'show-workouts', methods: ['GET'])]
    public function index(WorkoutRepository $workoutRepository): Response
    {

        $user=$this->security->getUser();
        $workouts = $workoutRepository->findAll();
        return $this->render('workout/showWorkouts.html.twig', [
            'controller_name' => 'WorkoutController',
            'workouts' => $workouts,
            'user' => $user,
        ]);
    }

    #[Route('/workouts/{workoutId}/start', name: 'start-workout', methods: ['GET'])]
    public function startWorkout(int $workoutId, ExerciseLogRepository $exerciseLogRepository): Response
    {

        $exerciseLogs = $exerciseLogRepository->findByWorkoutId($workoutId);

        return $this->render('workout/startWorkout.html.twig', [
            'exerciseLogs' => $exerciseLogs,
        ]);
    }

    #[Route('/workouts/new', name: 'new-workout', methods: ['GET'])]
    public function new(): Response
    {
        $workout = new Workout();

        $form = $this->createForm(WorkoutType::class, $workout,[
            'action' => $this->generateUrl('create-workout'),
            'method' => 'POST',
        ]);


        return $this->render('workout/addWorkout.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/workout', name: 'create-workout', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $workout = new Workout();

        $form = $this->createForm(WorkoutType::class, $workout);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $workout= $form->getData();
            $user=$this->security->getUser();
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


    #[Route('/workouts/{id}/edit', name: 'edit-workout', methods: ['GET'])]
    public function edit(int $id, WorkoutRepository $workoutRepository): Response
    {
        $workout = $workoutRepository->find($id);
        if (!$workout) {
            throw $this->createNotFoundException('No workout found for id ' . $id);
        }
        $form = $this->createForm(WorkoutType::class, $workout,[
            'action' => $this->generateUrl('update-workout', ['id'=>$id]),
            'method' => 'PATCH',]);
        return $this->render('workout/editWorkout.html.twig', [
            'form' => $form,
            'workout' => $workout,
        ]);
    }

    #[Route('/workouts/{id}', name: 'update-workout', methods: ['PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, WorkoutRepository $workoutRepository): Response
    {
        $workout = $workoutRepository->find($id);
        if (!$workout) {
            throw $this->createNotFoundException('No workout found for id ' . $id);
        }

        $form = $this->createForm(WorkoutType::class, $workout, [
            'method' => 'PATCH',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Workout Edited!');
            return $this->redirectToRoute('show-workouts');
        }
        return $this->render('workout/editWorkout.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/workouts/{id}', name: 'delete-workout', methods: ['GET','DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager,  WorkoutRepository $workoutRepository): Response
    {
        $workout = $workoutRepository->find($id);
        $entityManager->remove($workout);
        $entityManager->flush();
        return $this->redirectToRoute('show-workouts');
    }

}
