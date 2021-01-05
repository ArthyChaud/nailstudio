<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ProduitController extends AbstractController
{
    /**
     * @Route("/admin/show/stockProduit", name="admin_show_stock")
     */
    public function showStock(){
        return $this->render('admin/stocks/showStock.html.twig');
    }


}