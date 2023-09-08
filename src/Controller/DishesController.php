<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Form\DishesType;
use App\Repository\DishesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dishes', name: 'dishes.')]
class DishesController extends AbstractController
{
    //inject doctrine into the constructor of the controller to use it
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/', name: 'edit')]
    public function index(DishesRepository $dishRepo): Response
    {
        $dishes = $dishRepo->findAll();
        return $this->render('dishes/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request){
        $dish = new Dishes();
        
        //Formulat
        $form = $this->createForm(DishesType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //EntityManager
            $conn = $this->doctrine->getManager();
            $image = $form->get('image')->getData();

            if($image){
                //create a unique name for the file
                $filename = md5(uniqid()). '.'. $image->guessClientExtension();
            }

            //move the file to the images repository in public repository
            $image->move(
                $this->getParameter('images_folder'),
                $filename
            );

            //set the field image to the unique filename we created
            $dish->setImage($filename);

            //send the object to the database
            $conn->persist($dish);
            $conn->flush();//send data to the database

            return $this->redirect($this->generateUrl('dishes.edit'));
        }

        //Response
        return $this->render('dishes/create.html.twig', [
            'createForm' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, DishesRepository $dishRepo){
        //EntityManager
        $conn = $this->doctrine->getManager();
        $dish = $dishRepo->find($id);
        $conn->remove($dish);
        $conn->flush();

        //message
        $this->addFlash('success', 'the dish was removed carefully');

        return $this->redirect($this->generateUrl('dishes.edit'));
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(Dishes $dish){
        return $this->render('dishes/show.html.twig', [
            'dish' => $dish,
        ]);
    }
}
