<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\EditUserType;
use App\Form\Type\SignInType;
use App\Form\Type\UserType;
use App\Repository\ExerciseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        private Security $security,
    ){
    }

    #[Route('/users', name: 'show-users', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user=$this->security->getUser();
        if($user->getRole()=='user'){
            $users = $userRepository->findBy(['role' => 'trainer']);
        }else{
            $users = $userRepository->findAll();
        }
        return $this->render('user/showUsers.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'actualUser'=>$user,
        ]);
    }

    #[Route('/users/{id}', name: 'get-user', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('No user found for id ' . $id);
        }
        return $this->render('user/showUser.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/users/{id}/edit', name: 'edit-user', methods: ['GET'])]
    public function edit(int $id, UserRepository $userRepository): Response
    {
        $user=$userRepository->findUserById($id);

        $form = $this->createForm(EditUserType::class, $user,[
            'action' => $this->generateUrl('update-user', ['id'=>$id]),
            'method' => 'PATCH',]);
        return $this->render('user/editUser.html.twig', [
            'form' => $form,
            'user'=>$user,
        ]);
    }

    #[Route('/users/{id}', name: 'update-user', methods: ['PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = $userRepository->findUserById($id);

        $form = $this->createForm(EditUserType::class, $user, [
            'method' => 'PATCH',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'User Edited!');
            return $this->redirectToRoute('show-users');
        }
        return $this->render('user/editUser.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/user/{id}', name: 'delete-user', methods: ['GET','DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user=$userRepository->findUserById($id);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('show-users');
    }


}
