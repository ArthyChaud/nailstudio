<?php

namespace App\Controller\Admin;

use App\Entity\Agenda;
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
    * @Route("/admin/add/calendar", name="admin_add_calendar", methods={"GET","POST"})
    */
    public function addCalendar(Request $request)
    {
        if($request->getMethod() == 'GET'){
            return $this->render('/admin/agenda/addCalendar.html.twig');
        }

        $donnees['titre']=$_POST['titre'];
        $donnees['description']=$request->request->get('description');
        $donnees['start']=$request->request->get('start');
        $donnees['end']=$request->request->get('end');
        $donnees['color']=$request->request->get('color');

       // $erreurs=$this->validatorCalendar($donnees);
        if( empty($erreurs))
        {
            $dateStart = new \DateTime($donnees['start']);
            $dateEnd = new \DateTime($donnees['end']);
            $calendar = new Calendar();
            $calendar->setTitre($donnees['titre']);
            $calendar->setDescription($donnees['description']);
            $calendar->setStart($dateStart);
            $calendar->setEnd($dateEnd);
            $calendar->setBackgroundColor($donnees['color']);
            $this->getDoctrine()->getManager()->persist($calendar);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_show_agenda');
        }

        return $this->render('/admin/agenda/addAgenda.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs]);
    }
}