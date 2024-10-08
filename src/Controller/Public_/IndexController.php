<?php

namespace App\Controller\Public_;

use App\Entity\Line;
use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class IndexController extends AbstractController
{

    #[Route('/', name: 'public_index')]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_index');
        }
        return $this->render('public/index/index.html.twig', []);
    }

    #[Route('/g/{slug}', name: 'public_page')]
    public function publicUrl(User $user): Response
    {
        return $this->render('public/index/list.html.twig', [
            'list' => $user->getLines(),
        ]);
    }

    #[Route('/condition-utilisation', name: 'public_condition_use_fr')]
    public function condition_use(): Response
    {
        return $this->render('public/index/conditions-use_fr.html.twig');
    }


    #[Route('/foire-aux-questions', name: 'public_faq_fr')]

    public function faq(): Response
    {
        die;
//        return $this->render('public/index/faq_fr.html.twig');
    }

}
