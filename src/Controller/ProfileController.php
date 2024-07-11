<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{

    public function __construct(
        private Security $security,
    ){
    }
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}
