<?php

namespace App\Controller;

use App\Entity\CarouselLike;
use App\Entity\RDV;
use App\Form\MailFormType;
use App\Repository\CarouselLikeRepository;
use App\Repository\MediaRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AccueilController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(){
        return $this->render('accueil/accueil.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(MailFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();
            $surname = $form->get('surname')->getData();
            $emailAddress = $form->get('mail')->getData();
            $tel = $form->get('tel')->getData();
            $subject = $form->get('subject')->getData();
            $text = $form->get('text')->getData();

            $mail = new Email();
            $mail->from(new Address($emailAddress, $name . ' ' . $surname))
                ->to(new Address('alexis25.py@gmail.com'))
                ->subject($subject)
                ->text($text."\n\n"."Me contacter par mail : ".$emailAddress."\n"."Me contacter par téléphone : ".$tel);
            try {
                $mailer->send($mail);
            } catch (TransportExceptionInterface $e) {
                throw $e;
            }

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mentionLegales", name="mentionLegales")
     */
    public function mentionLegales(){
        return $this->render('mentionLegales/mentionLegales.html.twig');
    }

    /**
     * @Route("/realisations", name="realisations")
     */
    public function realisations(){
        return $this->render('realisations/realisations.html.twig');
    }

    /**
     * @Route("/reservations", name="reservations")
     */
    public function reservations(){
        if($this->getUser()!=null) {
            //dd($this->getUser()->getRoles());
            if ($this->getUser()->getRoles() == ['ROLE_ADMIN','ROLE_USER']) {
                $rdvs = $this->getDoctrine()->getRepository(RDV::class)->findAll();
            }
            else{
                $rdvs = $this->getDoctrine()->getRepository(RDV::class)->findBy(['user'=>$this->getUser()],['dateRdv'=>'ASC','heure'=>'ASC']);
            }
        }
        else{
            $rdvs = $this->getDoctrine()->getRepository(RDV::class)->findBy([],['dateRdv'=>'ASC','heure'=>'ASC']);
        }
        return $this->render('reservations/reservations.html.twig',['rdvs'=>$rdvs]);
    }

    /**
     * @Route("/services", name="services")
     * @param CarouselLikeRepository $carousel
     * @return Response
     */
    public function services(CarouselLikeRepository $carousel){
        return $this->render('services/services.html.twig', [
            'carousels' => $carousel->findAll()
        ]);
    }

}