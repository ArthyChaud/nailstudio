<?php

namespace App\Controller;

use App\Entity\CarouselLike;
use App\Form\CarouselLikeFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarouselLikeController extends AbstractController
{

    private $fileUploader;
    private $entityManager;

    public function __construct(FileUploader $fileUploader, EntityManagerInterface $entityManager)
    {
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/{page}/carousel/add/", name="admin_carousel_add")
     * @Route("/admin/{page}/carousel/edit/{id}", name="admin_carousel_edit")
     * @param null $page
     * @param Request $request
     * @param CarouselLike|null $carousel
     * @return Response
     */
    public function CarouselHandle($page, Request $request, CarouselLike $carousel = null): Response
    {

        $view = "edit";
        if (!$carousel) {
            $view = "add";
            $carousel = new CarouselLike();
        }

        $form = $this->createForm(CarouselLikeFormType::class, $carousel, array('attr' => [$page]));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carousel->setType($page);

            $this->entityManager->persist($carousel);
            $this->entityManager->flush();

            $this->addFlash('success',($view == "edit" ? 'éditée' : 'ajoutée') . ' avec succès');
            return $this->redirectToRoute($page);
        }

        return $this->render('carousel/handle.html.twig', [
            'form' => $form->createView(),
            'view' => $view
        ]);
    }

    /**
     * @Route("/admin/{page}/carousel/delete/{id}", name="admin_carousel_delete")
     * @param $page
     * @param CarouselLike $carousel
     * @return Response
     */
    public function CarouselDelete($page,CarouselLike $carousel): Response
    {

        if (!$carousel) {
            throw $this->createNotFoundException("Ce carousel n'existe pas");
        }

        $this->entityManager->remove($carousel);
        $this->entityManager->flush();

        $this->addFlash('success', 'Carousel supprimé avec succès');
        return $this->redirectToRoute($page);
    }
}
