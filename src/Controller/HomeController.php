<?php

namespace App\Controller;

use App\Repository\ExerciseLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods:'GET')]
    public function index(ExerciseLogRepository $exerciseLogRepository): Response
    {
        $exerciseLogs = $exerciseLogRepository->findTopExercises();
        return $this->render('components/home.html.twig', [
            'exerciseLogs'=>$exerciseLogs,
        ]);
    }
}