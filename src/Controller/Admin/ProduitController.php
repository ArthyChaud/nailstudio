<?php

namespace App\Controller\Admin;

use App\Entity\Marque;
use App\Entity\Produit;
use App\Entity\TypeProduit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;

class ProduitController extends AbstractController
{
    /**
     * @Route("/admin/show/produit", name="admin_show_produit")
     */
    public function showAllProduit(){
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy([],['typeProduit'=>'ASC','besoin'=>'ASC','libelle'=>'ASC']);
        return $this->render('admin/produit/showProduit.html.twig',['produits'=>$produits]);
    }

    /**
     * @Route("/admin/show/un/produit", name="admin_show_un_produit", methods={"POST"})
     */
    public function showProduit(Request $request){
        $libelle= $request->request->get('libelle');

        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy(['libelle'=>$libelle],[]);
        return $this->render('admin/produit/showProduit.html.twig',['produits'=>$produits]);
    }
    /**
     * @Route("/admin/add/besoin", name="admin_add_besoin")
     */
    public function addBesoin(Request $request){

        $entityManager = $this->getDoctrine()->getManager();
        $id= $request->request->get('id');
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit)  throw $this->createNotFoundException('No produit found for id '.$id);
        $produit->setBesoin(1);
        $entityManager->persist($produit);
        $entityManager->flush();
        return $this->redirectToRoute('admin_show_produit');
    }

    /**
     * @Route("/admin/add/produit", name="admin_add_produit", methods={"GET","POST"})
     */
    public function addProduit(Request $request)
    {
        $typeProduits = $this->getDoctrine()->getRepository(TypeProduit::class)->findBy([], ['libelle' => 'ASC']);
        $marques = $this->getDoctrine()->getRepository(Marque::class)->findBy([], ['libelle' => 'ASC']);

        if ($request->getMethod() == 'GET') {
            return $this->render('admin/produit/addProduit.html.twig', ['typeProduits' => $typeProduits,'marques' => $marques]);
        }

        if (!$this->isCsrfTokenValid('form_produit', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire produit');
        }
        $donnees['libelle'] = $_POST['libelle'];
        $donnees['stock'] = $request->request->get('stock');
        $donnees['prix'] = $request->request->get('prix');
        $donnees['typeProduitId'] = $request->request->get('typeProduitId');
        $donnees['marqueId'] = $request->request->get('marqueId');

        $erreurs = $this->validatorProduit($donnees);
        dump($erreurs);
        #dd($erreurs);
        if (empty($erreurs)) {
            $produit = new Produit();
            $produit->setLibelle($donnees['libelle']);
            $typeProduit = $this->getDoctrine()->getRepository(TypeProduit::class)->find($donnees['typeProduitId']);
            $produit->setTypeProduit($typeProduit);
            $marque = $this->getDoctrine()->getRepository(Marque::class)->find($donnees['marqueId']);
            $produit->setMarque($marque);
            $produit->setPrix($donnees['prix']);
            $produit->setStock($donnees['stock']);
            $produit->setBesoin(0);

            $this->getDoctrine()->getManager()->persist($produit);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_show_produit');
        }

        $typeProduits = $this->getDoctrine()->getRepository(TypeProduit::class)->findBy([], ['libelle' => 'ASC']);
        $marques = $this->getDoctrine()->getRepository(Marque::class)->findBy([], ['libelle' => 'ASC']);

        return $this->render('admin/produit/addProduit.html.twig', ['donnees' => $donnees, 'erreurs' => $erreurs, 'typeProduits' => $typeProduits,'marques' => $marques]);
    }
    /**
     * @Route("/admin/edit/{id}/produit", name="admin_edit_produit", methods={"GET"})
     * @Route("/admin/edit/produit",name="admin_edit_produit_valid",methods={"PUT"})
     */
    public function editProduit(Request $request, $id=null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $typeProduits = $this->getDoctrine()->getRepository(TypeProduit::class)->findBy([], ['libelle' => 'ASC']);
        $marques = $this->getDoctrine()->getRepository(Marque::class)->findBy([], ['libelle' => 'ASC']);

        $id=$request->get('id');

        if ($request->getMethod() == 'GET') {
            $produit = $entityManager->getRepository(Produit::class)->find($id);
            if (!$produit) throw $this->createNotFoundException('No produit found for id ' . $id);

            return $this->render('/admin/produit/editProduit.html.twig', ['typeProduits' => $typeProduits,'marques' => $marques, 'donnees' => $produit]);
        }
        if (!$this->isCsrfTokenValid('form_produit', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token formulaire produit');
        }

        $donnees['libelle'] = $_POST['libelle'];
        $donnees['stock'] = $request->request->get('stock');
        $donnees['prix'] = $request->request->get('prix');
        $donnees['typeProduitId'] = $request->request->get('typeProduitId');
        $donnees['marqueId'] = $request->request->get('marqueId');

        $erreurs = $this->validatorProduit($donnees);
        if (empty($erreurs)) {
            $produit = $entityManager->getRepository(Produit::class)->find($id);
            if (!$produit) throw $this->createNotFoundException('No produit found for id' . $id);

            $produit->setLibelle($donnees['libelle']);
            $typeProduit = $this->getDoctrine()->getRepository(TypeProduit::class)->find($donnees['typeProduitId']);
            $produit->setTypeProduit($typeProduit);
            $marque = $this->getDoctrine()->getRepository(Marque::class)->find($donnees['marqueId']);
            $produit->setMarque($marque);
            $produit->setPrix($donnees['prix']);
            $produit->setStock($donnees['stock']);
            $produit->setBesoin(0);

            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute('admin_show_produit');
        }
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        $typeProduits = $this->getDoctrine()->getRepository(TypeProduit::class)->findBy([], ['libelle' => 'ASC']);
        $marques = $this->getDoctrine()->getRepository(Marque::class)->findBy([], ['libelle' => 'ASC']);
        return $this->render('/admin/produit/editProduit.html.twig', ['typeProduits' => $typeProduits,'marques'=>$marques, 'donnees' => $produit, 'erreurs'=>$erreurs]);

    }

    /**
     * @Route("/admin/delete/produit", name="admin_delete_produit", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        if(!$this->isCsrfTokenValid('produit_delete', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire produit');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $id= $request->request->get('id');
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit)  throw $this->createNotFoundException('No produit found for id '.$id);
        $entityManager->remove($produit);
        $entityManager->flush();
        return $this->redirectToRoute('admin_show_produit');

    }

    /**
     * @Route("/admin/add/typeProduit", name="admin_add_typeProduit", methods={"GET","POST"})
     */
    public function addTypeProduit(Request $request)
    {
        if($request->getMethod() == 'GET'){
            return $this->render('/admin/produit/addTypeProduit.html.twig');
        }
        if(!$this->isCsrfTokenValid('form_typeProduit', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire type produit');
        }
        $donnees['libelle']=$_POST['libelle'];
        $erreurs=$this->validatorTypeProduit($donnees);
        dump($erreurs);
        if( empty($erreurs))
        {
            $typeProduit = new TypeProduit();
            $typeProduit->setLibelle($donnees['libelle']);

            $this->getDoctrine()->getManager()->persist($typeProduit);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_show_produit');
        }
        return $this->render('/admin/produit/addTypeProduit.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs]);
    }
    /**
     * @Route("/admin/add/marque", name="admin_add_marque", methods={"GET","POST"})
     */
    public function addMarque(Request $request)
    {
        if($request->getMethod() == 'GET'){
            return $this->render('/admin/produit/addMarque.html.twig');
        }
        if(!$this->isCsrfTokenValid('form_marque', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire marque');
        }
        $donnees['libelle']=$_POST['libelle'];
        $erreurs=$this->validatorTypeProduit($donnees);
        dump($erreurs);
        if( empty($erreurs))
        {
            $marque = new Marque();
            $marque->setLibelle($donnees['libelle']);

            $this->getDoctrine()->getManager()->persist($marque);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_show_produit');
        }
        return $this->render('/admin/produit/addMarque.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs]);
    }

    /**
     * @Route("/admin/increment/stock", name="admin_increment_stock", methods={"POST"})
     */
    public function incrementStock(Request $request)
    {
        $donnees['stock'] = $_POST['stock'];
        if($donnees['stock']==NULL OR !is_numeric($donnees['stock']))
            return $this->redirectToRoute('admin_show_produit');

        $entityManager = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if ($request->request->get('submitAction') == 'moins') {
            if($produit->getStock()-$donnees['stock']<0){
                $produit->setStock(0);
            }else{
                $produit->setStock($produit->getStock()-$donnees['stock']);
            }
        } elseif ($request->request->get('submitAction') == 'plus') {
            $produit->setStock($produit->getStock()+$donnees['stock']);
        }
        elseif ($request->request->get('submitAction') == 'égale') {
            $produit->setStock($donnees['stock']);
        }

        $entityManager->persist($produit);
        $entityManager->flush();
        return $this->redirectToRoute('admin_show_produit');
    }
    /**
     * @Route("/admin/increment/besoin", name="admin_increment_besoin", methods={"POST"})
     */
    public function incrementBesoin(Request $request)
    {
        $donnees['besoin'] = $_POST['besoin'];
        if($donnees['besoin']==NULL OR !is_numeric($donnees['besoin']))
            return $this->redirectToRoute('admin_show_produit');

        $entityManager = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if ($request->request->get('submitAction') == 'moins') {
            if($produit->getBesoin()-$donnees['besoin']<0){
                $produit->setBesoin(0);
            }else{
                $produit->setBesoin($produit->getBesoin()-$donnees['besoin']);
            }
        } elseif ($request->request->get('submitAction') == 'plus') {
            $produit->setBesoin($produit->getBesoin()+$donnees['besoin']);
        }
        elseif ($request->request->get('submitAction') == 'égale') {
            $produit->setBesoin($donnees['besoin']);
        }

        $entityManager->persist($produit);
        $entityManager->flush();
        return $this->redirectToRoute('admin_show_produit');
    }

    private function validatorTypeProduit($donnees)
    {
        $erreurs = array();

        if(strcmp($donnees['libelle'],'')==0)
            $erreurs['libelle'] = 'Veuillez entrer un libelle';

        return $erreurs;
    }

    private function validatorProduit($donnees)
    {
        $erreurs = array();

        if(strcmp($donnees['libelle'],'')==0)
            $erreurs['libelle'] = 'Veuillez entrer un libelle';

        if($donnees['typeProduitId']==NULL)
            $erreurs['typeProduitId'] = 'Veuillez entrer un type';

        if($donnees['marqueId']==NULL)
            $erreurs['marqueId'] = 'Veuillez entrer une marque';

        if($donnees['prix']==NULL OR !is_numeric($donnees['prix']))
            $erreurs['prix'] = 'Veuillez entrer un prix';

        if($donnees['stock']==NULL OR !is_numeric($donnees['stock']))
            $erreurs['stock'] = 'Veuillez entrer un stock';

        return $erreurs;
    }

}