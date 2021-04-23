<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\EmailModel;
use App\Entity\User;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Services\EmailSender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController
{


    /**
     * @Route("/", name="contact_new", methods={"GET","POST"})
     */
    public function new(Request $request, EmailSender $emailSender): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();
            //Envoie de mail
            $user = (new User())
                ->setEmail('dahoniinfo@gmail.com')
                ->setFirstname('Comores Store')
                ->setLastname('Shopping');

            $email = (new EmailModel())
                ->setTitle('Bonjour ' . $user->getNameComplet())
                ->setSubject("Contact depuis le formaulaire du site")
                ->setContent(
                    "<br> Message de :" . $contact->getEmail()
                        . "<br> Nom: " . $contact->getName()
                        . "<br> Sujet " . $contact->getSubject()
                        . "<br><br>" . $contact->getContent()
                );

            $emailSender->sendEmailByMailJet($user, $email);

            $contact = new Contact();
            $form = $this->createForm(ContactType::class, $contact);
            $this->addFlash('contact_success', 'Votre message est envoyé avec succès. Un conseiller vous répondra très rapidement!');
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('contact_error', 'Une erreur est survenue, merci de réessayer!');
        }
        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }
}
