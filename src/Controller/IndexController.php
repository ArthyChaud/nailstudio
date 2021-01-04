<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index_index")
     */
    public function index(Request $request)
    {
        if($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('accueil');
        }
        if($this->isGranted('ROLE_CLIENT')) {
            return $this->redirectToRoute('rdv_show');
        }
        return $this->redirectToRoute('accueil');
    }
}