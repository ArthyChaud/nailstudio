<?php


namespace App\Controller\Admin;


use App\Entity\Produit;
use App\Entity\RDV;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Calendar;
use App\Entity\TypeService;
use App\Entity\User;
use http\Client\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Validator\Constraints\Date;
use Twig\Environment;

class ServiceController extends AbstractController
{
    /**
     * @Route("/admin/show/service", name="admin_show_service")
     */
    public function showService(Request $request)
    {
        $services = $this->getDoctrine()->getRepository(TypeService::class)->findAll();
        return $this->render('admin/service/showService.html.twig',['services'=>$services]);

    }
    /**
     * @Route("/admin/add/service", name="admin_add_service")
     */
    public function addService(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $services = $this->getDoctrine()->getRepository(TypeService::class)->findAll();
        if ($request->getMethod() == 'GET') {
            return $this->render('admin/service/addService.html.twig');
        }
        $donnees['libelle']= $request->request->get('libelle');
        $donnees['color']= $request->request->get('color');

        $erreurs = $this->validatorService($donnees);
        if(empty($erreurs)){
            $services = new TypeService();
            $services->setLibelle($donnees['libelle']);
            $services->setColor($donnees['color']);
            $em->persist($services);
            $em->flush();
            return $this->redirectToRoute('admin_show_service');

        }
        return $this->render('admin/service/addService.html.twig',['erreurs'=>$erreurs]);

    }
    /**
     * @Route("/admin/test", name="admin_test") , methods={"POST"})
     */
    public function test(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $service = $entityManager->getRepository(TypeService::class)->find(88);

        //$calendar=$entityManager->getRepository(Calendar::class)->getCalendarWithTypeServices($service->getID());
        $calendar = $entityManager->getRepository(RDV::class)->findBy(['typeService' => $service], []);


    }
    /**
     * @Route("/admin/show/service/edit", name="admin_add_service_edit") , methods={"POST"})
     */
    public function serviceEdit(Request $request)
    {
        $donnees = json_decode($request->getContent());
/*
        $donnees[0] = id,
        $donnees[1] = libelle,
        $donnees[2] = color,
*/
        $em = $this->getDoctrine()->getManager();
        //je verifier que donnees existe et n'est pas vide
        if(isset($donnees[0]) && !empty($donnees[0])
            && isset($donnees[1]) && !empty($donnees[1])
            && isset($donnees[2]) && !empty($donnees[2])) {
            //permet de parcourir le tab donnees dans le foreach
            $index=0;
            //pour chaque idService
            foreach ($donnees[0] as $id) {
                //je recupère le service
                $service = $em->getRepository(TypeService::class)->find($id);
                //si le libelle n'est pas null
                if (strcmp($donnees[1][$index], '') != 0) {
                    //je change le libelle par celui reçu
                    $service->setLibelle($donnees[1][$index]);
                }
                //je change la couleur par celle reçu
                $service->setColor($donnees[2][$index]);

                /*  je récupère tout les id calendars liées au service
                    grâce à une requete sql dans le repository de Calendar
                */
                $idCalendars=$em->getRepository(Calendar::class)->getCalendarWithTypeServices($service->getID());
                if(isset($idCalendars) && !empty($idCalendars)){
                    foreach ($idCalendars as $idCalendar){
                        //je recupère le calendar liées à l'id
                        $calendar = $em->getRepository(Calendar::class)->find($idCalendar);
                        //je met à jour la couleur
                        $calendar->setBackgroundColor($donnees[2][$index]);
                        if (strcmp($donnees[1][$index], '') != 0) {
                            //je change le titre des events par celui reçu
                            $calendar->setTitre($donnees[1][$index]);
                        }
                        $em->persist($calendar);
                    }
                }
                $em->persist($service);
                $index++;
            }
        }

        $em->flush();
        return $this->redirectToRoute('admin_show_service');
    }
    /**
     * @Route("/admin/delete/service", name="admin_delete_service", methods={"DELETE"})
     */
    public function delete(Request $request)
    {
        if(!$this->isCsrfTokenValid('service_delete', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire produit');
        }
        $em = $this->getDoctrine()->getManager();
        $id= $request->request->get('id');
        $service = $em->getRepository(TypeService::class)->find($id);
        if (!$service)  throw $this->createNotFoundException('No produit found for id '.$id);
        //suppression des events liées au service
        $idCalendars=$em->getRepository(Calendar::class)->getCalendarWithTypeServices($service->getID());
        foreach ($idCalendars as $idCalendar){
            //je recupère le calendar liées à l'id
            $calendar = $em->getRepository(Calendar::class)->find($idCalendar);
            $em->remove($calendar);
        }
        //suppression des rdvs liées au service
        $rdvs = $this->getDoctrine()->getRepository(RDV::class)->findBy(['typeService'=>$service],[]);
        foreach ($rdvs as $rdv){
            $em->remove($rdv);
        }
        //suppression du service
        $em->remove($service);
        $em->flush();
        return $this->redirectToRoute('admin_show_service');

    }


    private function validatorService($donnees)
    {
        $erreurs=[];
        if(strcmp($donnees['libelle'],'')==0){
            $erreurs['libelle']='Veuillez entrer un libelle';
        }
        return $erreurs;
    }
}