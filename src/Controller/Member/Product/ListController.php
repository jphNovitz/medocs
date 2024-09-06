<?php

/**
 * @author novitz jean-philippe <hello@jphnovitz.be>
 * @copyright 2021-2022
 */

namespace App\Controller\Member\Product;

use App\Entity\Line;
use App\Form\DeleteFormType;
use App\Form\LineType;
use App\Repository\LineRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/member/list')]
class ListController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private LineRepository         $lineRepositoryRepository,
                                private UserRepository         $userRepository,
                                private Security               $security,
                                private RequestStack           $requestStack)
    {}

    #[Route('', name: "member_line_index")]
    public function index(): Response
    {
        if (!$list = $this->lineRepositoryRepository->getAll())
            return $this->RedirectToRoute('member_line_new');

        return $this->Render('member/product/list/index.html.twig', [
            'list' => $list
        ]);
    }

    #[Route('/new', name: "member_line_new")]
    public function new(Request $request): Response
    {
        $this->requestStack->getCurrentRequest()->getSession()->remove('referer');
        $line = new Line();
        $form = $this->createForm(LineType::class, $line);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $line->setUser($this->userRepository->findOneBy([
                    'id' => $this->security->getUser()->getId()
                ]));
                $this->entityManager->persist($line);
                $this->entityManager->flush();
                $this->addFlash('success', 'Ligne ajouté');

                return $this->RedirectToRoute('member_line_new');

            } catch (DriverException $e) {
                die;
            }
        }

        return $this->Render('member/product/list/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/update', name: "member_line_update", methods: ["GET", "PUT"])]
    public function update(Request $request, Line $line = null): Response
    {
        if (!$line) {
            $this->$this->addFlash('error', 'N\'existe pas');
            return $this->RedirectToRoute("member_line_index");
        }
        $form = $this->createForm(LineType::class, $line, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush();
                $this->$this->addFlash('success', 'modifié');

                return $this->RedirectToRoute("member_line_index");

            } catch (DriverException $ORMException) {
                die('erreur');
            }
        }

        return $this->Render('member/product/list/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: "member_line_delete", methods: ["GET", "DELETE"])]
    public function delete(Request $request, Line $line = null): Response
    {
        if (!$line) {
            $this->$this->addFlash('error', 'N\'existe pas');
            return $this->RedirectToRoute("member_line_index");
        }

        $defaultData = ['message' => 'Voulez vous effacer ' . $line->getName() . ' ?'];
        $form = $this->createForm(DeleteFormType::class, $defaultData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('yes')->isClicked()):
                try {
                    $this->entityManager->remove($line);
                    $this->entityManager->flush();
                    $this->$this->addFlash('success', 'supprimé');
                    return $this->RedirectToRoute("member_line_index");
                } catch (DriverException $exception) {
                    die('erreur');
                }
            else:
                $this->$this->addFlash('notice', 'annulé');

                return $this->RedirectToRoute("member_line_index");
            endif;
        }
        return $this->Render('member/product/list/delete.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
