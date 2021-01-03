<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     */
    public function contact(){
        return $this->render('contact/contact.html.twig');
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
        return $this->render('reservations/reservations.html.twig');
    }

    /**
     * @Route("/services", name="services")
     */
    public function services(){
        return $this->render('services/services.html.twig');
    }

}