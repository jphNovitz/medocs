<?php

/**
 * @author novitz jean-philippe <hello@jphnovitz.be>
 * @copyright 2021-2022
 */

namespace App\Controller\Admin\Product;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/admin/product')]
class ProductController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private ProductRepository      $productRepository,
                                UserRepository                 $userRepository,
                                Security                       $security,)
    {
    }

    #[Route('/', name: 'admin_product_index')]
    public function index(): Response
    {
        if (!$list = $this->productRepository->getAll())
            return $this->redirectToRoute('admin_product_new');

        return $this->render('admin/product/list/index.html.twig', [
            'list' => $list
        ]);
    }

    #[Route('/new', name: 'admin_product_new')]
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        if (!$request->isMethod('POST')) {
            $request->getSession()->set('previous_page', $request->headers->get('referer'));
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($product);
                $this->entityManager->flush();
                $this->addFlash('success', 'Produit ajouté');

//                if ($referer = $this->session->get('referer')) {
//                    $this->session->remove('referer');
//                    return $this->redirectToRoute($referer);
//                } else
//                $referer = $request->headers->get('referer');

                // Vérifier si le referer existe et rediriger
                $previousPage = $request->getSession()->get('previous_page', $this->generateUrl('admin_product_new'));

                // Supprimer l'URL de la session
                $request->getSession()->remove('previous_page');

                return $this->redirect($previousPage);

            } catch (DriverException $e) {
                die;
            }
        }

        return new Response($this->render('admin/product/new.html.twig', [
            'form' => $form->createView()
        ]));
    }

    #[Route('/{id}/update', name: 'admin_product_update', methods: ["GET", "PUT"])]
    public function update(Request $request, Product $product = null): Response
    {

        if (!$product) {
            $this->addFlash('error', 'N\'existe pas');
            return $this->redirectToRoute("admin_product_index");
        }

        $form = $this->createForm(ProductType::class, $product, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush();
                $this->addFlash('success', 'modifié');
                return $this->redirectToRoute("admin_product_index");

            } catch (DriverException $exception) {
                die('erreur');
            }
        }
        return $this->render('admin/product/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_product_delete', methods: ["GET", "DELETE"])]
    public function delete(Request $request, Product $product = null): Response
    {

        if (!$product) {
            $this->addFlash('error', 'N\'existe pas');

            return $this->redirectToRoute("admin_product_index");
        }

        $defaultData = ['message' => 'Voulez vous effacer ' . $product->getName() . ' ?'];
        $form = $this->createFormBuilder(null, $defaultData)
            ->add('yes', SubmitType::class, ['label' => 'Oui Supprimer'])
            ->add('no', SubmitType::class, ['label' => 'Non Annuler'])
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('yes')->isClicked()):
                try {
                    $this->entityManager->remove($product);
                    $this->entityManager->flush();
                    $this->addFlash('success', 'supprimé');

                    return $this->redirectToRoute("admin_product_index");

                } catch (DriverException $exception) {
                    die('erreur');
                }
            else:
                $this->addFlash('notice', 'annulé');
                return $this->redirectToRoute("admin_product_index");
            endif;
        }
        return $this->render('admin/product/delete.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
