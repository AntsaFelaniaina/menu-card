<?php

namespace App\Controller;

use App\Repository\DishesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/menu', name: 'menu')]
    public function menu(DishesRepository $disheRepo): Response
    {

        $dishes = $disheRepo->findAll();

        return $this->render('menu/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }
}
