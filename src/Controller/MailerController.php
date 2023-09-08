<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/mail', name: 'mail')]
    public function sendEMail(MailerInterface $mailer): Response
    {
        $email = (new Email())
                ->from('table2@menucard.com')
                ->to('antsa@ritec.mg')
                ->subject('Order')
                ->text('Extra fries');
                
        $mailer->send($email);

        return new Response('Email sent');
    }
}
