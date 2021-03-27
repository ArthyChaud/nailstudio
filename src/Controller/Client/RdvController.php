<?php
namespace App\Controller\Client;

use App\Entity\RDV;
use App\Entity\TypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Validator\Constraints\Date;
use Twig\Environment;

class RdvController extends AbstractController
{
    /**
     * @Route("/PrendreRendezVous/getRendezVous", name="rdv_get")
     */
    public function rdvJson(Request $request)
    {
        $rdvs = $this->getDoctrine()->getRepository(RDV::class)->findAll();
        $dates = array();
        foreach ($rdvs as $key => $rdv){
            $dates[$key]['date'] = $rdv->getDateRdv();
            $dates[$key]['heure'] = $rdv->getHeure();

        }
        return new JsonResponse($dates);
    }
    /**
     * @Route("/PrendreRendezVous", name="rdv_date")
     */
    public function rdvAdd(Request $request)
    {
        /*
                if ($request->request->get('date_autre') != null) {
                    $literalTime = \DateTime::createFromFormat("Y-m-d", $request->request->get('date_autre'));
                } else {
                    $literalTime = new\DateTime('now');
                }

                $date = new \DateTime($expire_date);
                $rdvs = $this->getDoctrine()->getRepository(RDV::class)->findBy(['dateRdv' => $date]);
                if ($rdvs != null) {
                    foreach ($rdvs as $rdv) {
                        $heures[$rdv->getHeure()]["select"] = True;
                    }
                }
        */
        $heures = array("08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30",
            "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30",
        );
        $typeServices = $this->getDoctrine()->getRepository(TypeService::class)->findBy([], ['libelle' => 'ASC']);

        $literalTime = new\DateTime('now');
        $expire_date = $literalTime->format("Y-m-d");
        return $this->render('Client/Rdv/RdvAddForm.html.twig',['date'=>$expire_date,'heures'=>$heures,'typeServices'=>$typeServices]);
    }

    /**
     * @Route("/PrendreRendezVous/validation", name="rdv_date_valide")
     */
    public function rdvAddValide(Request $request)
    {
        $donnees['date'] = $request->request->get('date');
        $donnees['heure'] = $request->request->get('horaireRadioBouton');
        $donnees['typeService'] = $request->request->get('typeService');
        $erreurs=$this->rdvErreur($donnees);
        if (empty($erreurs)) {
            $rdv = new RDV();
            $rdv->setDateRdv(\DateTime::createFromFormat('Y-m-d',$donnees['date']));
            $rdv->setHeure($donnees['heure']);
            $typeService=$this->getDoctrine()->getRepository(TypeService::class)->find($donnees['typeService']);
            $rdv->setTypeService($typeService);
            $rdv->setUser($this->getUser());
            $rdv->setValider(false);

            $this->getDoctrine()->getManager()->persist($rdv);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservations');
        }
        $heures = array("08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30",
            "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30",
        );

        $typeServices = $this->getDoctrine()->getRepository(TypeService::class)->findBy([], ['libelle' => 'ASC']);
        return $this->render('Client/Rdv/RdvAddForm.html.twig',['date'=>$donnees['date'],'heures'=>$heures,'typeServices'=>$typeServices,'erreurs'=>$erreurs]);

    }

    public function rdvErreur($donnees)
    {
        $erreurs = array();
        if($donnees['heure'] == null)
            $erreurs['heure'] = 'Veuillez sélectionner une heure valide';
        if($donnees['typeService'] == null)
            $erreurs['typeService'] = 'Veuillez sélectionner un service';

        return $erreurs;
    }

    /**
     * @Route("/annulationRendezVous", name="rdv_delete")
     */
    public function rdvDelete(Request $request)
    {
        if(!$this->isCsrfTokenValid('rdv_delete', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire depense');
        }
        $id= $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $rdv=$this->getDoctrine()->getRepository(RDV::class)->find($id);
        $entityManager->remove($rdv);
        $entityManager->flush();
        return $this->redirectToRoute('admin_show_agenda');

    }
    /**
     * @Route("/admin/validerRdv", name="admin_rdv_validation")
     */
    public function rdvValidation(Request $request)
    {
        if(!$this->isCsrfTokenValid('rdv_validation', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire rdv validation');
        }
        $id= $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $rdv=$this->getDoctrine()->getRepository(RDV::class)->find($id);
        $rdv->setValider(True);
        $entityManager->persist($rdv);
        $entityManager->flush();
        return $this->redirectToRoute('admin_show_agenda');

    }
    /**
     * @Route("/admin/test", name="admin_rdv_test")
     */
    public function rdvTest(Request $request)
    {
        dd('test');

    }

}