<?php

namespace App\Controller\Admin;

use App\Entity\Accounting;
use App\Entity\CategoryAccounting;
use App\Form\AccountingForm;
use App\Repository\AccountingRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountingController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("admin/accounting", name="admin_accounting")
     * @param AccountingRepository $accountingRepository
     * @return Response
     */
    public function index(AccountingRepository $accountingRepository)
    {
        $accounting = $accountingRepository->findAll();
        return $this->render('admin/accounting/accounting.html.twig', ['accounting' => $accounting]);
    }

    /**
     * @Route("/admin/accounting/add/", name="admin_accounting_add")
     * @Route("/admin/accounting/edit/{id}", name="admin_accounting_edit")
     * @param Request $request
     * @param Accounting|null $accounting
     * @return Response
     */
    public function AccountingHandle(Request $request, Accounting $accounting = null): Response
    {
        $view = "edit";
        if (!$accounting) {
            $view = "add";
            $accounting = new Accounting();
        }

        $form = $this->createForm(AccountingForm::class, $accounting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = $form->get('date')->getData();
            $prix = $form->get('prix')->getData();
            $libelle = $form->get('libelle')->getData();
            $id = $form->get('categoryaccounting')->getData();
            $categorie = $this->getDoctrine()->getRepository(CategoryAccounting::class)->find($id);
            $accounting->setDate($date)
                ->setPrix($prix)
                ->setLibelle($libelle)
                ->setCategoryAccounting($categorie);

            $this->entityManager->persist($accounting);
            $this->entityManager->flush();

            $this->addFlash('success',($view == "edit" ? 'éditée' : 'ajoutée') . ' avec succès');
            return $this->redirectToRoute('admin_accounting');
        }

        return $this->render('admin/accounting/handle.html.twig', [
            'form' => $form->createView(),
            'view' => $view
        ]);
    }

    /**
     * @Route("/admin/accounting/delete/{id}", name="admin_accounting_delete")
     * @param Accounting $accounting
     * @return Response
     */
    public function AccountingDelete(Accounting $accounting): Response
    {
        if (!$accounting) {
            throw $this->createNotFoundException("Cette ligne n'existe pas");
        }

        $this->entityManager->remove($accounting);
        $this->entityManager->flush();

        $this->addFlash('success', 'Ligne supprimé avec succès');
        return $this->redirectToRoute('admin_accounting');
    }

    /**
     * @Route("/admin/accounting/show", name="admin_accounting_show")
     * @param AccountingRepository $accountingRepository
     * @return Response
     */
    public function CompteResultat(AccountingRepository $accountingRepository)
    {
        $accounting = $accountingRepository->findAll();
        return $this->render('admin/accounting/show.html.twig', ['accounting' => $accounting]);
    }

    /**
     * @Route("/admin/accounting/stats", name="admin_accounting_stats")
     * @param AccountingRepository $accountingRepository
     * @return Response
     */
    public function AccountingStats(AccountingRepository $accountingRepository)
    {
        $stats = $accountingRepository->findAll();
        return $this->render('admin/accounting/stats.html.twig', [
            'stats' => $stats
        ]);
    }
}
