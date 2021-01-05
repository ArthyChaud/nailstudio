<?php

namespace App\Controller\Admin;

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
    public function showProduit(){
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findBy([],['libelle'=>'ASC']);
        return $this->render('admin/produit/showProduit.html.twig',['produits'=>$produits]);
    }
    /**
     * @Route("/admin/show/produit/besoin", name="admin_show_besoin")
     */
    public function showBesoin(){
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findByExampleField();
        //dd($produits);
        return $this->render('admin/produit/showBesoin.html.twig',['produits'=>$produits]);
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
     * @Route("/admin/add/multiple/besoin", name="admin_add_multiple_besoin")
     */
    public function addMultipleBesoin(Request $request){

        $entityManager = $this->getDoctrine()->getManager();
        $id= $request->request->get('id');
        $nbBesoin = $request->request->get('nbBesoin');
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit)  throw $this->createNotFoundException('No produit found for id '.$id);
        $produit->setBesoin($nbBesoin);
        $entityManager->persist($produit);
        $entityManager->flush();
        return $this->redirectToRoute('admin_show_besoin');
    }
    /**
     * @Route("/admin/delete/besoin", name="admin_delete_besoin")
     */
    public function deleteBesoin(Request $request){

        $entityManager = $this->getDoctrine()->getManager();
        $id= $request->request->get('id');
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit)  throw $this->createNotFoundException('No produit found for id '.$id);
        $produit->setBesoin(0);
        $entityManager->persist($produit);
        $entityManager->flush();
        return $this->redirectToRoute('admin_show_besoin');
    }
    /**
     * @Route("/admin/add/produit", name="admin_add_produit", methods={"GET","POST"})
     */
    public function addProduit(Request $request)
    {
        $typeProduits = $this->getDoctrine()->getRepository(TypeProduit::class)->findBy([], ['libelle' => 'ASC']);
        if ($request->getMethod() == 'GET') {
            return $this->render('admin/produit/addProduit.html.twig', ['typeProduits' => $typeProduits]);
        }

        if (!$this->isCsrfTokenValid('form_produit', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire produit');
        }
        $donnees['libelle'] = $_POST['libelle'];
        $donnees['stock'] = $request->request->get('stock');
        $donnees['prix'] = $request->request->get('prix');
        $donnees['typeProduitId'] = $request->request->get('typeProduitId');

        $erreurs = $this->validatorProduit($donnees);
        dump($erreurs);
        #dd($erreurs);
        if (empty($erreurs)) {
            $produit = new Produit();
            $produit->setLibelle($donnees['libelle']);
            $typeProduit = $this->getDoctrine()->getRepository(TypeProduit::class)->find($donnees['typeProduitId']);
            $produit->setTypeProduit($typeProduit);
            $produit->setPrix($donnees['prix']);
            $produit->setStock($donnees['stock']);
            $produit->setBesoin(0);

            $this->getDoctrine()->getManager()->persist($produit);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_show_produit');
        }

        // A modifier : Utiliser la méthode findBy du Repository : TypeProduitRepository (trier les types de produits par libelle)
        $typeProduits = $this->getDoctrine()->getRepository(TypeProduit::class)->findBy([], ['libelle' => 'ASC']);
        // fin A modifier
        return $this->render('admin/produit/addProduit.html.twig', ['donnees' => $donnees, 'erreurs' => $erreurs, 'typeProduits' => $typeProduits]);
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
     * @Route("/admin/edit/{id}/produit", name="admin_edit_produit", methods={"GET"})
     * @Route("/admin/edit/produit",name="admin_edit_produit_valid",methods={"PUT"})
     */
    public function editProduit(Request $request, $id=null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $typeProduits = $this->getDoctrine()->getRepository(TypeProduit::class)->findBy([], ['libelle' => 'ASC']);
        $id=$request->get('id');

        if ($request->getMethod() == 'GET') {
            $produit = $entityManager->getRepository(Produit::class)->find($id);
            if (!$produit) throw $this->createNotFoundException('No produit found for id ' . $id);

            return $this->render('/admin/produit/editProduit.html.twig', ['typeProduits' => $typeProduits, 'donnees' => $produit]);
        }
        if (!$this->isCsrfTokenValid('form_produit', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token formulaire produit');
        }

        $donnees['libelle'] = $_POST['libelle'];
        $donnees['stock'] = $request->request->get('stock');
        $donnees['prix'] = $request->request->get('prix');
        $donnees['typeProduitId'] = $request->request->get('typeProduitId');

        $erreurs = $this->validatorProduit($donnees);
        if (empty($erreurs)) {
            $produit = $entityManager->getRepository(Produit::class)->find($id);
            if (!$produit) throw $this->createNotFoundException('No produit found for id' . $id);

            $produit->setLibelle($donnees['libelle']);
            $typeProduit = $this->getDoctrine()->getRepository(TypeProduit::class)->find($donnees['typeProduitId']);
            $produit->setTypeProduit($typeProduit);
            $produit->setPrix($donnees['prix']);
            $produit->setStock($donnees['stock']);
            $produit->setBesoin(0);

            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirectToRoute('admin_show_produit');
        }
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        $typeProduits = $this->getDoctrine()->getRepository(TypeProduit::class)->findBy([], ['libelle' => 'ASC']);
        return $this->render('/admin/produit/editProduit.html.twig', ['typeProduits' => $typeProduits, 'donnees' => $produit, 'erreurs'=>$erreurs]);

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

        if($donnees['prix']==NULL OR !is_numeric($donnees['prix']))
            $erreurs['prix'] = 'Veuillez entrer un prix';

        if($donnees['stock']==NULL)
            $erreurs['stock'] = 'Veuillez entrer un stock';

        return $erreurs;
    }


}