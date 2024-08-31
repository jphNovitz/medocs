<?php
/**
 * @author novitz jean-philippe <hello@jphnovitz.be>
 * @copyright 2021-2022
 */

namespace App\Controller\Member\Dose;

use App\Entity\Day;
use App\Form\DayType;
use App\Form\DeleteFormType;
use App\Repository\DayRepository;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/dose/day")]
class DayController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private DayRepository          $dayRepository,)
    {
    }

    #[Route("", name: "member_day_index")]
    public function index(): Response
    {
        return new Response ($this->render('admin/dose/day/index.html.twig', [
            'list' => $this->dayRepository->getAll(),
        ]));
    }

    #[Route("/new", name: "member_day_new", methods: ["GET", "POST"])]
    public function create(Request $request): Response
    {

        $day = new Day();
        $form = $this->createForm(DayType::class, $day);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($day);
                $this->entityManager->flush();

                $this->addFlash('success', 'ajouté');

                return $this->RedirectToRoute("member_day_index");

            } catch (DriverException $e) {
                die('erreur');
            }
        }
        return $this->Render('admin/dose/day/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/{id}/update", name: "app_member_dose_day_update", methods: ["GET", "PUT"])]
    public function update(Request $request, Day $day = null): Response
    {

        if (!$day) {
            $this->addFlash('error', 'N\'existe pas');

            return $this->RedirectToRoute("member_day_index");
        }

        $form = $this->createForm(DayType::class, $day, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush();
                $this->addFlash('success', 'modifié');
                return $this->RedirectToRoute("member_day_index");

            } catch (DriverException $e) {
                die('erreur');
            }
        }
        return $this->Render('admin/dose/day/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/{id}/delete", name: "member_day_delete", methods: ["GET", "DELETE"])]
    public function delete(Request $request, Day $day = null): Response
    {
        if (!$day) {
            $this->addFlash('error', 'N\'existe pas');
            return $this->RedirectToRoute("member_day_index");
        }

        $defaultData = ['message' => 'Voulez vous effacer ' . $day->getName() . ' ?'];

        $form = $this->createForm(DeleteFormType::class, $defaultData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('yes')->isClicked()):
                try {
                    $this->entityManager->remove($day);
                    $this->entityManager->flush();
                    $this->addFlash('success', 'supprimé');

                    return $this->RedirectToRoute("member_day_index");

                } catch (DriverException $e) {
                    die('erreur');
                }
            else:
                $this->addFlash('notice', 'annulé');
                return $this->RedirectToRoute("member_day_index");
            endif;

        }
        return $this->Render('admin/dose/day/delete.html.twig', [
            'form' => $form->createView(),
            'default_data' => $defaultData
        ]);
    }
}
