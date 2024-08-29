<?php
/**
 * @author novitz jean-philippe <hello@jphnovitz.be>
 * @copyright 2021-2022
 */

namespace App\Controller\Admin;

use App\Form\ShareListType;
use App\Form\UrlType;
use App\Repository\LineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/admin')]
class IndexController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager,
                                private LineRepository $lineRepository,
                                Security $security)
    {
    }

#[Route('/', name:'admin_index')]
    public function index(): Response
    {
        $products = $this->lineRepository->getAllUserLines($this->getUser());;

        $url_form = $this->createForm(UrlType::class);
        $send_form = $this->createForm(ShareListType::class);

        return $this->render('admin/index.html.twig', [
            'list' => $products,
            'url_form' => $url_form->createView(),
            'send_form' => $send_form->createView()
        ]);
    }

    #[Route('/url', name:'admin_index_url')]
    public function updateUrl(Request $request): Response
    {
        $this->getUser()->setUrl($request->request->get('url')['url']);
        $this->entityManager->flush();

        $this->addFlash('success', 'url modifiée');
        return  $this->redirectToRoute('admin_index');
    }

    #[Route('/send', name:'admin_index_send_email')]
    public function sendMyUrl(MailerInterface $mailer, Request $request): Response
    {
        $lines = $this->lineRepository->getAllUserLines($this->user->getId());
        $email = (new TemplatedEmail())
            ->from(new Address($this->getParameter('system_mail'), 'Jeanphi de Medocs.be'))
            ->to($request->request->get('share_list')['email'])
            //->cc('cc@medocs.be')
            //->bcc('bcc@medocs.be')
            //->replyTo('fabien@medocs.be')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($this->user->getEmail() . ' vous envoie sa liste de médicaments')
            ->htmlTemplate('emails/list/html-list.html.twig')
//            ->textTemplate('emails/list/text-list.html.twig')
            ->context([
                'list' => $lines
            ]);

        try {
            $mailer->send($email);
            $this->addFlash('success', 'email envoyé');
            return $this->redirectToRoute('admin_index');
        } catch (TransportExceptionInterface $exception) {
            die('error');
        }
    }
}
