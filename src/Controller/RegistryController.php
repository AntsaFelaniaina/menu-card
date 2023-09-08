<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistryController extends AbstractController
{
    //inject doctrine into the constructor of the controller to use it
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/registry', name: 'registry')]
    public function registry(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $regform = $this->createFormBuilder()
        ->add('username', TextType::class,[
            'label' => 'employee'])
        ->add('password', RepeatedType::class,[
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => 'Password'], 
            'second_options' => ['label' => 'Repeate Password']
        ])
        ->add('submit', SubmitType::class)
        ->getForm()
        ;

        $regform->handleRequest($request);

        if($regform->isSubmitted()){
            //get the data from the input
            $input = $regform->getData();
            var_dump($input);

            //create a user
            $user = new Users();
            $user->setUsername($input['username']);
            $user->setPassword(
                $passwordEncoder->hashPassword($user, $input['password'])
            );

            //Manage database
            $conn = $this->doctrine->getManager();
            $conn->persist($user);
            $conn->flush();

            //Redirect to the homepage after registration
            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('registry/index.html.twig', [
            'registrationForm' => $regform->createView(),
        ]);
    }
}
