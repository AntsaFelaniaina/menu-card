<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
        $table = 'table1';
        $text = 'Please, bring more salt';
        $email = (new TemplatedEmail())
                ->from('table2@menucard.com')
                ->to('toavina1205@gmail.com')
                ->subject('Order')
                ->htmlTemplate('mailer/mail.html.twig')
                ->context([
                    'table' => $table,
                    'text' => $text
                ]);
                
        $mailer->send($email);

        return new Response('Email sent');
    }
}
