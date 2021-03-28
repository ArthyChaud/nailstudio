<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Entity\RDV;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use function MongoDB\BSON\fromJSON;

class CalendarController extends AbstractController
{
    /**
     * @Route("/admin/show/calendrier", name="admin_show_calendrier")
     */
    public function showCalendar(Request $request)
    {
        $rdvs = $this->getDoctrine()->getRepository(RDV::class)->findBy(['valider'=>false],[]);

        $events = $this->getDoctrine()->getRepository(Calendar::class)->findAll();
        $tabRdvs = [];
        foreach ($events as $event){
            $tabRdvs[] = [
                'id'=> $event->getId(),
                'title'=> $event->getTitre(),
                'start'=> $event->getStart()->format('Y-m-d H:i:s'),
                'end'=> $event->getEnd()->format('Y-m-d H:i:s'),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
            ];
        }
        $data = json_encode($tabRdvs);

        return $this->render('/admin/calendrier/showCalendar.html.twig',['data'=>$data,'rdvs'=>$rdvs]);
    }
    /**
    * @Route("/admin/add/calendar", name="admin_add_calendar", methods={"GET","POST"})
    */
    public function addCalendar(Request $request)
    {
        if($request->getMethod() == 'GET'){
            return $this->render('/admin/calendrier/addCalendar.html.twig');
        }

        $donnees['titre']=$_POST['titre'];
        //$donnees['description']=$request->request->get('description');
        $donnees['start']=$request->request->get('start');
        $donnees['end']=$request->request->get('end');
        $donnees['color']=$request->request->get('color');

        $erreurs=$this->validatorCalendar($donnees);
        if( empty($erreurs))
        {
            $dateStart = new \DateTime($donnees['start']);
            $dateEnd = new \DateTime($donnees['end']);
            $calendar = new Calendar();
            $calendar->setTitre($donnees['titre']);
            //$calendar->setDescription($donnees['description']);
            $calendar->setStart($dateStart);
            $calendar->setEnd($dateEnd);
            $calendar->setBackgroundColor($donnees['color']);
            $this->getDoctrine()->getManager()->persist($calendar);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_show_calendrier');
        }

        return $this->render('/admin/calendrier/addCalendar.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs]);
    }
    /**
     * @Route("/admin/delete/calendar", name="admin_delete_calendar", methods={"DELETE"})
     */
    public function deleteCalendar(Request $request)
    {
        $id = json_decode($request->getContent());
        $em = $this->getDoctrine()->getManager();
        $calendar = $em->getRepository(Calendar::class)->find($id);
        if($calendar != null) {
            if ($calendar->getRdv() != null) {
                $rdv = $em->getRepository(RDV::class)->find($calendar->getRdv());
                $em->remove($calendar);
                $em->remove($rdv);
            } else {
                $em->remove($calendar);
            }
            $em->flush();
        }

        return $this->redirectToRoute('admin_show_calendrier');

    }

    private function validatorCalendar($donnees)
    {
        $erreurs = array();

        if(strcmp($donnees['titre'],'')==0)
            $erreurs['titre'] = 'Veuillez entrer un titre';
        if ($donnees['start'] == NULL)
            $erreurs['start'] = 'Veuillez entrer une date de début';
        if ($donnees['end'] == NULL)
            $erreurs['end'] = 'Veuillez entrer une date de fin';
        $dateStart = new \DateTime($donnees['start']);
        $dateEnd = new \DateTime($donnees['end']);
        if($dateStart>$dateEnd)
            $erreurs['end'] = 'Veuillez entrer une date valide';
        if($dateStart==$dateEnd) {
            $erreurs['end'] = 'Veuillez entrer des dates valident';
            $erreurs['start'] = 'Veuillez entrer des dates valident';
        }


        return $erreurs;
    }
}