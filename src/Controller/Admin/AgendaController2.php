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

class AgendaController2 extends AbstractController
{
    /**
     * @Route("/admin/show/agenda", name="admin_show_agenda")
     */
    public function showAgenda(Request $request)
    {
        $agendas= $this->getDoctrine()->getRepository(Agenda::class)->findBy([],['date'=>'ASC']);
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

        return $this->render('/admin/agenda/showAgenda.html.twig',['data'=>$data,'agendas'=>$agendas,'rdvs'=>$rdvs]);
    }

    /**
     * @Route("/admin/add/agenda", name="admin_add_agenda", methods={"GET","POST"})
     */
    public function addAgenda(Request $request)
    {
        if($request->getMethod() == 'GET'){
            return $this->render('/admin/agenda/addAgenda.html.twig');
        }

        $donnees['libelle']=$_POST['libelle'];
        $donnees['description']=$request->request->get('description');
        $donnees['date']=$request->request->get('date');
        $donnees['horaire']=$request->request->get('horaire');

        $erreurs=$this->validatorAgenda($donnees);
        dump($erreurs);
        #dd($erreurs);
        if( empty($erreurs))
        {
            $agenda = new Agenda();
            $agenda->setLibelle($donnees['libelle']);
            $agenda->setDescription($donnees['description']);
            $agenda->setHoraire($donnees['horaire']);
            $agenda->setDate(\DateTime::createFromFormat('d/m/Y',$donnees['date']));

            $this->getDoctrine()->getManager()->persist($agenda);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_show_agenda');
        }
        return $this->render('/admin/agenda/addAgenda.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs]);
    }

    /**
     * @Route("/admin/delete/agenda", name="admin_delete_agenda", methods={"DELETE"})
     */
    public function deleteAgenda(Request $request)
    {
        if(!$this->isCsrfTokenValid('agenda_delete', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire agenda');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $id= $request->request->get('id');
        $agenda = $entityManager->getRepository(Agenda::class)->find($id);
        if (!$agenda)  throw $this->createNotFoundException('No agenda found for id '.$id);
        $entityManager->remove($agenda);
        $entityManager->flush();
        return $this->redirectToRoute('admin_show_agenda');

    }
    /**
     * @Route("/edit/{id}/agenda", name="admin_edit_agenda", methods={"GET"})
     * @Route("/edit/agenda",name="admin_edit_agenda_valid",methods={"PUT"})
     */
    public function editAgenda(Request $request, $id=null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id=$request->get('id');

        if ($request->getMethod() == 'GET') {
            $agenda = $entityManager->getRepository(Agenda::class)->find($id);
            if (!$agenda) throw $this->createNotFoundException('No agenda found for id ' . $id);

            $dateAgenda = $agenda->getDate();
            return $this->render('/admin/agenda/editAgenda.html.twig', ['donnees' => $agenda, 'date' => $dateAgenda]);
        }
        if (!$this->isCsrfTokenValid('form_agenda', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token formulaire agenda');
        }
        $donnees['libelle']=$_POST['libelle'];
        $donnees['description']=$request->request->get('description');
        $donnees['date']=$request->request->get('date');
        $donnees['horaire']=$request->request->get('horaire');

        $donnees['horaire']=$request->request->get('horaire');

        $erreurs = $this->validatorAgenda($donnees);
        if (empty($erreurs)) {
            $agenda = $entityManager->getRepository(Agenda::class)->find($id);
            if (!$agenda) throw $this->createNotFoundException('No agenda found for id' . $id);
            $agenda->setLibelle($donnees['libelle']);
            $agenda->setDate(\DateTime::createFromFormat('d/m/Y', $donnees['date']));
            $agenda->setDescription($donnees['description']);
            $entityManager->persist($agenda);
            $entityManager->flush();
            return $this->redirectToRoute('admin_show_agenda');
        }
        $depense = $entityManager->getRepository(Agenda::class)->find($id);
        $dateAgenda = $depense->getDate();
        return $this->render('/admin/agenda/editAgenda.html.twig', ['donnees' => $depense, 'date' => $dateAgenda,'erreurs'=>$erreurs]);

    }

    public function validatorAgenda($donnees)
    {
        $erreurs = array();

        if(strcmp($donnees['libelle'],'')==0)
            $erreurs['libelle'] = 'Veuillez entrer un libelle';

        if(strcmp($donnees['horaire'],'')==0)
            $erreurs['horaire'] = 'Veuillez entrer un horaire';

        if(strcmp($donnees['description'],'')==0)
            $erreurs['description'] = 'Veuillez entrer une description';

        $dateConvert = \DateTime::createFromFormat('d/m/Y', $donnees['date']);
        if ($dateConvert == NULL)
            $erreurs['date'] = 'la date doit être au format JJ/MM/AAAA';
        else {
            if ($dateConvert->format('d/m/Y') !== $donnees['date'])
                $erreurs['date'] = 'la date n\'est pas valide (format jj/mm/aaaa)';
        }
        return $erreurs;
    }
}