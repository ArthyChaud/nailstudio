<?php
namespace App\Controller\Client;

use App\Entity\Calendar;
use App\Entity\RDV;
use App\Entity\TypeService;
use App\Entity\User;
use http\Client\Response;
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

    public function rdvIntoCalendar($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $rdv=$this->getDoctrine()->getRepository(RDV::class)->find($id);

        /*-----------------------------
         création d'une enité calendar
        -----------------------------*/
        /*création date Start*/
        $StringDateStart = $rdv->getDateRdv()->format('Y-m-d')."T".$rdv->getHeure();
        $dateStart = new \DateTime($StringDateStart);

        /*création date End*/
        $heure = $rdv->getHeure();
        if($heure[0]=='1' and $heure[1]=='7' and $heure[3]=='3'){
            $heure[1]='8';
            $heure[3]='0';
        }else if($heure[0]=='0' and $heure[1]=='9' and $heure[3]=='3'){
            $heure[0]='1';
            $heure[1]='0';
            $heure[3]='0';
        }else if($heure[3]=='0'){
            $heure[3]='3';

        }else if($heure[3]=='3'){
            $heure[1]=strval(intval($heure[1])+1);
            $heure[3]='0';
        }
        $StringDateEnd = $rdv->getDateRdv()->format('Y-m-d')."T".$heure;
        $dateEnd = new \DateTime($StringDateEnd);


        $calendar = new Calendar();
        $type_service=$this->getDoctrine()->getRepository(TypeService::class)->find($rdv->getTypeService());
        $calendar->setTitre($type_service->getLibelle());
        $user=$this->getDoctrine()->getRepository(User::class)->find($rdv->getUser());

        $calendar->setDescription($user->getUsername());
        $calendar->setStart($dateStart);
        $calendar->setEnd($dateEnd);
        $calendar->setBackgroundColor($type_service->getColor());
        $rdv=$this->getDoctrine()->getRepository(RDV::class)->find($id);

        $calendar->setRdv($rdv);
        $this->getDoctrine()->getManager()->persist($calendar);

        $entityManager->flush();
        return $this->redirectToRoute('admin_show_agenda');

    }
    /**
     * @Route("/admin/show/agenda/rdvValiderRefuser", name="admin_show_agenda_rdvValiderRefuser" , methods={"POST"})
     */
    public function rdvValiderRefuser(Request $request)
    {
        $donnees = json_decode($request->getContent());
        $em = $this->getDoctrine()->getManager();

        if(isset($donnees[0])  && !empty($donnees[0])){
            foreach ($donnees[0] as $id){
                $rdv = $this->getDoctrine()->getRepository(RDV::class)->find($id);
                $rdv->setValider(True);
                $this->rdvIntoCalendar($rdv->getId());
            }
        }
        if(isset($donnees[1])  && !empty($donnees[1])){
            foreach ($donnees[1] as $id){
                $rdv = $this->getDoctrine()->getRepository(RDV::class)->find($id);
                $em->remove($rdv);
            }
        }
        $em->flush();
        return $this->redirectToRoute('admin_show_agenda');
    }

}