<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\EditUserType;
use App\Form\Type\SignInType;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/signup', name: 'sign-up', methods: ['GET','POST'])]
    public function signup(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user= $form->getData();
            // here the user gets the role as a "trainer"
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Sign up successful!');
            return $this->redirectToRoute('sign-in');
        }

        return $this->render('user/addTrainer.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/add-user', name: 'add-user', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user= $form->getData();
            // here the user gets the role "user"
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'User added successfully!');
            return $this->redirectToRoute('show-users');
        }

        return $this->render('user/addNewUser.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/users', name: 'show-users')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/showUsers.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
        ]);
    }

   #[Route('/signin', name: 'sign-in', methods: ['GET','POST'])]
   public function sign(Request $request): Response
   {
       $form = $this->createForm(SignInType::class);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           return $this->redirectToRoute('exercises');
       }
       return $this->render('user/signIn.html.twig', [
           'form' => $form,
       ]);
   }


    #[Route('/user/{id}/edit', name: 'edit-user', methods: ['GET'])]
    public function edit(int $id, UserRepository $userRepository): Response
    {
        $user=$userRepository->findUserById($id);
        if (!$user) {
            throw $this->createNotFoundException('No user found for id ' . $id);
        }
        $form = $this->createForm(EditUserType::class, $user,[
            'action' => $this->generateUrl('update-user', ['id'=>$id]),
            'method' => 'PATCH',]);
        return $this->render('user/editUser.html.twig', [
            'form' => $form,
            'user'=>$user,
        ]);
    }

    #[Route('/user/{id}', name: 'update-user', methods: ['PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = $userRepository->findUserById($id);
        if (!$user) {
            throw $this->createNotFoundException('No user found for id ' . $id);
        }

        $form = $this->createForm(EditUserType::class, $user, [
            'method' => 'PATCH',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'User Edited!');
            return $this->redirectToRoute('edit-user', [
                'id' => $id,
            ]);
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
