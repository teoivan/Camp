<?php

namespace App\Controller;

use App\Entity\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class TypeController extends AbstractController
{
    #[Route('/type', name: 'create_type', methods: ['GET','POST'])]
    public function createType(EntityManagerInterface $entityManager): Response
    {
        $type = new Type();
        $type->setName('Flexibility');

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($type);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new type with id '.$type->getId());
    }
}
