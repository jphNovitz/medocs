<?php

/**
 * @author novitz jean-philippe <hello@jphnovitz.be>
 * @copyright 2021-2022
 */

namespace App\Controller\Member\Product;

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

#[Route('/member/product')]
class ProductController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private ProductRepository      $productRepository,
                                UserRepository                 $userRepository)
    {
    }

    #[Route('/', name: 'member_product_index')]
    public function index(): Response
    {
        if (!$list = $this->productRepository->getAll())
            return $this->redirectToRoute('member_product_new');

        return $this->render('admin/product/list/index.html.twig', [
            'list' => $list
        ]);
    }

    #[Route('/new', name: 'member_product_new')]
    public function new(Request $request): Response
    {
        dd($this->entityManager->getRepository(Product::class)->findAll());
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

//        if (!$request->isMethod('POST')) {
//            $request->getSession()->set('previous_page', $request->headers->get('referer'));
//        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $this->addFlash('success', 'Produit ajouté');

            if ($referer = $request->getSession()->get('referer')) {
                $request->getSession()->remove('referer');
                return $this->redirectToRoute($referer);
            } else
                $referer = $request->headers->get('referer');

            // Vérifier si le referer existe et rediriger
            $previousPage = $request->getSession()->get('previous_page', $this->generateUrl('member_product_new'));

            // Supprimer l'URL de la session
            $request->getSession()->remove('previous_page');

            return $this->redirect($previousPage);
        }

        return new Response($this->render('member/product/new.html.twig', [
            'form' => $form->createView()
        ]));
    }

    #[Route('/api/new', name: 'member_product_api_new', methods: ["POST"])]
    public function newApi(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);


        if ($name = $data["product[name]"]) {
            $product = new Product();
            $product->setName($name);

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Données enregistrées avec succès.',
                'data' => [
                    'id' => $product->getId(),
                    'name' => $product->getName()
                ]
            ], 200);
        } else {
            return $this->json([
                'success' => false,
                'message' => 'Données non enregistrées.',
            ], 400);
        }
    }

    #[Route('/{id}/update', name: 'member_product_update', methods: ["GET", "PUT"])]
    public function update(Request $request, Product $product = null): Response
    {

        if (!$product) {
            $this->addFlash('error', 'N\'existe pas');
            return $this->redirectToRoute("member_product_index");
        }

        $form = $this->createForm(ProductType::class, $product, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush();
                $this->addFlash('success', 'modifié');
                return $this->redirectToRoute("member_product_index");

            } catch (DriverException $exception) {
                die('erreur');
            }
        }
        return $this->render('admin/product/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'member_product_delete', methods: ["GET", "DELETE"])]
    public function delete(Request $request, Product $product = null): Response
    {

        if (!$product) {
            $this->addFlash('error', 'N\'existe pas');

            return $this->redirectToRoute("member_product_index");
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

                    return $this->redirectToRoute("member_product_index");

                } catch (DriverException $exception) {
                    die('erreur');
                }
            else:
                $this->addFlash('notice', 'annulé');
                return $this->redirectToRoute("member_product_index");
            endif;
        }
        return $this->render('admin/product/delete.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
