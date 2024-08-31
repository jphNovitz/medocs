<?php
/**
 * @author novitz jean-philippe <hello@jphnovitz.be>
 * @copyright 2021-2022
 */

namespace App\Controller\Member\Dose;


use App\Entity\Frequency;
use App\Form\DeleteFormType;
use App\Form\FrequencyType;
use App\Repository\FrequencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dose/frequency')]
class FrequencyController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private FrequencyRepository    $frequencyRepository)
    {
    }

    #[Route('', name: 'member_frequency_index')]
    public function index(): Response
    {
        return $this->render('admin/dose/frequency/index.html.twig', [
            'list' => $this->frequencyRepository->getAll()
        ]);
    }

    #[Route('/create', name: 'member_frequency_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $frequency = new Frequency();
        $form = $this->createForm(FrequencyType::class, $frequency);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($frequency);
            $this->entityManager->flush();

            $this->addFlash('success', 'ajouté');

            return $this->redirectToRoute("member_frequency_index");
        }
        return $this->render('admin/dose/frequency/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'member_frequency_edit', methods: ['GET', 'PUT'])]
    public function update(Request $request, Frequency $frequency = null): Response
    {
        if (!$frequency) {
            $this->addFlash('error', 'N\'existe pas');

            return $this->redirectToRoute("member_frequency_index");
        }

        $form = $this->createForm(FrequencyType::class, $frequency, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'modifié');

            return $this->redirectToRoute("member_frequency_index");
        }
        return $this->render('admin/dose/frequency/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'member_frequency_delete', methods: ['GET', 'DELETE'])]
    public function delete(Request $request, Frequency $frequency = null): Response
    {

        if (!$frequency) {
            $this->addFlash('error', 'N\'existe pas');

            return $this->redirectToRoute("member_frequency_index");
        }

        $defaultData = ['message' => 'Voulez vous effacer ' . $frequency->getName() . ' ?'];
        $form = $this->createForm(DeleteFormType::class, $defaultData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('yes')->isClicked()):

                $this->entityManager->remove($frequency);
                $this->entityManager->flush();
                $this->addFlash('success', 'supprimé');

                return $this->redirectToRoute("member_frequency_index");
            else:
                $this->addFlash('notice', 'annulé');
                return $this->redirectToRoute("member_frequency_index");
            endif;
        }
        return $this->render('admin/dose/frequency/delete.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
