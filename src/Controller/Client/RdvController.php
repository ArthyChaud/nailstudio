<?php
namespace App\Controller\Client;

use App\Entity\RDV;
use App\Entity\TypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Validator\Constraints\Date;
use Twig\Environment;

class RdvController extends AbstractController
{

    /**
     * @Route("/PrendreRendezVous", name="rdv_date")
     */
    public function rdvAdd(Request $request)
    {
        $heures = array(
            "08:00" => array(
                "libelle" => "08:00",
                "select" => False,
            ),
            "08:30" => array(
                "libelle" => "08:30",
                "select" => False,
            ),
            "09:00" => array(
                "libelle" => "09:00",
                "select" => False,
            ),
            "09:30" => array(
                "libelle" => "09:30",
                "select" => False,
            ),
            "10:00" => array(
                "libelle" => "10:00",
                "select" => False,
            ),
            "10:30" => array(
                "libelle" => "10:30",
                "select" => False,
            ),
            "11:00" => array(
                "libelle" => "11:00",
                "select" => False,
            ),
            "11:30" => array(
                "libelle" => "11:30",
                "select" => False,
            ),
            "12:00" => array(
                "libelle" => "12:00",
                "select" => False,
            ),
            "12:30" => array(
                "libelle" => "12:30",
                "select" => False,
            ),
            "13:00" => array(
                "libelle" => "13:00",
                "select" => False,
            ),
            "13:30" => array(
                "libelle" => "13:30",
                "select" => False,
            ),
            "14:00" => array(
                "libelle" => "14:00",
                "select" => False,
            ),
            "14:30" => array(
                "libelle" => "14:30",
                "select" => False,
            ),
            "15:00" => array(
                "libelle" => "15:00",
                "select" => False,
            ),
            "15:30" => array(
                "libelle" => "15:30",
                "select" => False,
            ),
            "16:00" => array(
                "libelle" => "16:00",
                "select" => False,
            ),
            "16:30" => array(
                "libelle" => "16:30",
                "select" => False,
            ),
            "17:00" => array(
                "libelle" => "17:00",
                "select" => False,
            ),
            "17:30" => array(
                "libelle" => "17:30",
                "select" => False,
            ),
        );

        $typeServices = $this->getDoctrine()->getRepository(TypeService::class)->findBy([], ['libelle' => 'ASC']);

        if ($request->request->get('date_autre') != null) {
            $literalTime = \DateTime::createFromFormat("Y-m-d", $request->request->get('date_autre'));
        } else {
            $literalTime = new\DateTime('now');
        }

        $expire_date = $literalTime->format("Y-m-d");
        $date = new \DateTime($expire_date);
        $rdvs = $this->getDoctrine()->getRepository(RDV::class)->findBy(['dateRdv' => $date]);
        if ($rdvs != null) {
        foreach ($rdvs as $rdv) {
            $heures[$rdv->getHeure()]["select"] = True;
        }
    }
        return $this->render('Client/Rdv/RdvAddForm.html.twig',['date'=>$expire_date,'heures'=>$heures,'typeServices'=>$typeServices]);
    }

    /**
     * @Route("/PrendreRendezVous/validation", name="rdv_date_valide")
     */
    public function rdvAddValide(Request $request)
    {
        $donnees['date'] = $request->request->get('date');
        $donnees['heure'] = $request->request->get('heure');
        $donnees['typeService'] = $request->request->get('typeService');

        $erreurs=null;
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
        return $this->redirectToRoute('reservations');

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
        return $this->redirectToRoute('reservations');

    }
}