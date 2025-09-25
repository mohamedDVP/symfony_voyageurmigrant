<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
    #[Route('/test-email')]
    public function send(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('no-reply@voyageurmigrant.com')
            ->to('test@example.com') // Peu importe, ça arrivera dans Mailtrap
            ->subject('Test Mailtrap')
            ->text('Hello depuis Symfony !');

        $mailer->send($email);

        return new Response('Email envoyé 🚀');
    }
}
