<?php
/**
 * @author novitz jean-philippe <hello@jphnovitz.be>
 * @copyright 2021-2022
 */

namespace App\Controller\Member\Dose;

use App\Entity\Dose;
use App\Entity\Frequency;
use App\Entity\Moment;
use App\Form\DeleteFormType;
use App\Form\DoseType;
use App\Repository\DoseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dose')]
class DoseController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em,
                                private DoseRepository         $doseRepository)
    {}

    #[Route('', name: 'member_dose_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/dose/dose/index.html.twig', [
            'list' => $this->doseRepository->getAll(),
        ]);
    }

    #[Route('/new', name: 'member_dose_new', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {

        $dose = new Dose();
        $form = $this->createForm(DoseType::class, $dose);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $this->em->persist($dose);
                $this->em->flush();

                $this->addFlash('success', 'ajouté');

                $previousPage = $request->getSession()->get('previous_page', $this->generateUrl('member_dose_new'));
                $request->getSession()->remove('previous_page');

        }
        return $this->render('admin/dose/dose/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/new', name: 'member_dose_api_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
//        dd($request->getContent());
        $data = json_decode($request->getContent(), true);
//                dd($data);
        $dose = new Dose();
        if ($data["dose[frequency]"] == "autre") {
            $frequency = new Frequency();
            $frequency->setName($data["dose[frequencyNew][name]"]);
            $this->em->persist($frequency);
            $dose->setFrequency($frequency);
        } else {
            $frequency = $this->em->getRepository(Frequency::class)->find($data["dose[frequency]"]);
            $dose->setFrequency($frequency);
        }

        if ($data["dose[moment]"] == "autre") {
            $moment = new Moment();
            $moment->setName($data["dose[momentNew][name]"]);
            $this->em->persist($moment);
            $dose->setMoment($moment);
        } else {
            $moment = $this->em->getRepository(Moment::class)->find($data["dose[moment]"]);
            $dose->setMoment($moment);
        }

        $this->em->persist($dose);
        $this->em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Données enregistrées avec succès.',
            'data' => [
                'id' => $dose->getId(),
                'name' => $dose->getName()
            ]
        ], 200);

    }
    
    #[Route('/{id}/update', name: 'member_dose_update', methods: ['GET', 'PUT'])]
    public function update(Request $request, Dose $dose = null): Response
    {
        if (!$dose) {
            $this->addFlash('error', 'N\'existe pas');

            return $this->redirectToRoute("member_dose_index");
        }

        $form = $this->createForm(DoseType::class, $dose, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $this->em->flush();

                $this->addFlash('success', 'modifié');

                return $this->redirectToRoute("member_dose_index");
        }
        return $this->render('admin/dose/dose/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'member_dose_delete', methods: ['GET', 'DELETE'])]
    public function delete(Request $request, Dose $dose= null): Response
    {
        if (!$dose) {
            $this->addFlash('error', 'N\'existe pas');

            return $this->redirectToRoute("member_dose_index");
        }

        $defaultData = ['message' => 'Voulez vous effacer ' . $dose . ' ?'];

        $form = $this->createForm(DeleteFormType::class, $defaultData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('yes')->isClicked()):
                    $this->em->remove($dose);
                    $this->em->flush();

                    $this->addFlash('success', 'supprimé');
                    return $this->redirectToRoute("member_dose_index");
            else:
                $this->addFlash('notice', 'annulé');
                return $this->redirectToRoute("member_dose_index");
            endif;
        }
        return $this->render('admin/dose/dose/delete.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
