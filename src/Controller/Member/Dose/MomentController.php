<?php
/**
 * @author novitz jean-philippe <hello@jphnovitz.be>
 * @copyright 2021-2022
 */

namespace App\Controller\Member\Dose;

use App\Entity\Moment;
use App\Form\DeleteFormType;
use App\Form\MomentType;
use App\Repository\MomentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dose/moment')]
class MomentController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em,
                                private MomentRepository       $momentRepository,)
    {
    }

    #[Route('', name: 'member_moment_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/dose/moment/index.html.twig', [
            'list' => $this->momentRepository->getAll(),
        ]);
    }

    #[Route('/new', name: 'member_moment_new', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {

        $moment = new Moment();
        $form = $this->createForm(MomentType::class, $moment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($moment);
            $this->em->flush();

            $this->addFlash('success', 'ajouté');

            return $this->redirectToRoute("member_moment_index");
        }
        return $this->render('admin/dose/moment/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/dose/moment/{id}/update",
     *     name="member_moment_update",
     *     methods={"GET", "PUT"})
     */
    #[Route('/{id}/edit', name: 'member_moment_edit', methods: ['GET', 'PUT'])]
    public function update(Request $request, Moment $moment = null): Response
    {

        if (!$moment) {
            $this->addFlash('error', 'N\'existe pas');

            return $this->redirectToRoute("member_moment_index");
        }

        $form = $this->createForm(MomentType::class, $moment, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'modifié');

            return $this->redirectToRoute("member_moment_index");
        }
        return $this->render('admin/dose/moment/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/dose/moment/{id}/delete",
     *     name="member_moment_delete",
     *     methods={"GET", "DELETE"})
     */
    #[Route('/{id}/delete', name: 'member_moment_delete', methods: ['GET', 'DELETE'])]
    public function delete(Request $request, Moment $moment = null): Response
    {
        if (!$moment) {
            $this->addFlash('error', 'N\'existe pas');
            return $this->redirectToRoute("member_moment_index");
        }

        $defaultData = ['message' => 'Voulez vous effacer ' . $moment->getName() . ' ?'];
        $form = $this->createForm(DeleteFormType::class, $defaultData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('yes')->isClicked()):
                $this->em->remove($moment);
                $this->em->flush();
                $this->addFlash('success', 'supprimé');

                return $this->redirectToRoute("member_moment_index");

            else:
                $this->addFlash('notice', 'annulé');
                return $this->redirectToRoute("member_moment_index");
            endif;
        }
        return $this->render('admin/dose/moment/delete.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
