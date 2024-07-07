<?php
namespace App\Controller;

use App\Entity\ExerciseLog;
use App\Form\Type\ExerciseLogType;
use App\Repository\ExerciseLogRepository;
use App\Repository\ExerciseRepository;
use App\Repository\WorkoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExerciseLogController extends AbstractController
{
    #[Route('/exercise-log/{id}', name: 'add-exercise-log', methods: ['GET', 'POST'])]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager, ExerciseRepository $exerciseRepository): Response
    {
        $exerciseLog = new ExerciseLog();
        $exercise = $exerciseRepository->find($id);
        if (!$exercise) {
            throw $this->createNotFoundException('The exercise does not exist');
        }

        $form = $this->createForm(ExerciseLogType::class, $exerciseLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exerciseLog = $form->getData();
            $exerciseLog->setExercise($exercise);
            $exerciseLog->setStartTime(new \DateTime());
            $entityManager->persist($exerciseLog);
            $entityManager->flush();

            $this->addFlash('success', 'Exercise log added successfully!');

            return $this->redirectToRoute('add-exercise-log', ['id' => $id]);
        }

        return $this->render('exercise_log/addExerciseLog.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('workout/exercise-log/{id}', name: 'show-exercise-log', methods: ['GET'])]
    public function index(int $id, ExerciseLogRepository $exerciseLogRepository): Response
    {
        $exercises = $exerciseLogRepository->findByWorkoutId($id);

        return $this->render('exercise_log/showExerciseLog.html.twig', [
            'exerciseLogs' => $exercises,
        ]);
    }
}