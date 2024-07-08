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
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{

    #[Route('/users', name: 'show-users', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/showUsers.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
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

    #[Route('/users/{role}/new', name: 'new-user', methods: ['GET'])]
    public function new(string $role): Response
    {
        $user = new User();


        $form = $this->createForm(UserType::class, $user,[
            'action' => $this->generateUrl('create-user',['role'=>$role]),
            'method' => 'POST',
        ]);

        return $this->render('user/addNewUser.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/users/{role}', name: 'create-user', methods: ['POST'])]
    public function create(string $role,Request $request, EntityManagerInterface $entityManager): Response
    {
        $newUser = new User();

        $form = $this->createForm(UserType::class, $newUser, [
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newUser= $form->getData();
            $newUser->setRole($role);
            // here the user gets the role "user"
            $entityManager->persist($newUser);
            $entityManager->flush();
            $this->addFlash('success', 'User added successfully!');
            if($newUser->getRole() == 'user'){
                return $this->redirectToRoute('show-users');
            }else{
                return $this->redirectToRoute('sign-in');
            }
        }

        return $this->render('user/addNewUser.html.twig', [
            'form' => $form,
        ]);

    }


   #[Route('/signin', name: 'sign-in', methods: ['GET','POST'])]
   public function sign(Request $request): Response
   {
       $form = $this->createForm(SignInType::class);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           return $this->redirectToRoute('home');
       }
       return $this->render('user/signIn.html.twig', [
           'form' => $form,
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
            return $this->redirectToRoute('get-user', [
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
