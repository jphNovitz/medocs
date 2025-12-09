<?php

/**
 * @author novitz jean-philippe <bonjour@jphiweb.be>
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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/member/product')]
class ProductController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

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
}
