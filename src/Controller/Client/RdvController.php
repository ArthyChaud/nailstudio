<?php
namespace App\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

class RdvController extends AbstractController
{
    /**
     * @Route("/client/PrendreRendezVous", name="client_rdv_add")
     */
    public function rdvAdd(Request $request)
    {

    }
}