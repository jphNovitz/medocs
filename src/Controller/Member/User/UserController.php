<?php

namespace App\Controller\Member\User;

use App\Entity\User;
use App\Form\DeleteFormType;
use App\Form\UserType;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

;

use Symfony\Component\Security\Core\Security;

/**
 * Class UserController
 * @package App\Controller\Admin\User
 */
#[Route('/admin/')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface      $em,
        private UserPasswordHasherInterface $passwordHasher,
        private Security                    $security,)
    {
    }

    #[Route('profile/update', name: "member_profile_update", methods: ['GET', 'PUT'])]
    public function updateInfos(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, null, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            $plainPassword = $form->getData()['plainPassword'];
            $repeatPassword = $form->getData()['repeatPassword'];
            if (!empty($plainPassword)) {
                if ($plainPassword === $repeatPassword) {
                    if ($this->passwordHasher->isPasswordValid($user, $form->getData()['oldPassword'])) {
//                        $user->setPassword(
//                            $this->passwordHasher->hash(
//                                $user,
//                                $plainPassword
//                            )
//                        );
                        $hashedPassword = $this->passwordHasher->hashPassword(
                            $user,
                            $form->getData()['plainPassword']
                        );
                        $user->setPassword($hashedPassword);
                    } else $this->addFlash('error', 'les deux mots de passes ne sont pas identiques');
                }
            }

            try {
                $this->em->flush();
                $this->addFlash('success', 'modifié');

                return $this->redirectToRoute("member_profile_update");
            } catch (DriverException $exception) {
                // Log erreur

                // Ajouter un message flash pour l'utilisateur
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour du profil. Veuillez réessayer.');

                // Rediriger vers la même page ou une page d'erreur
                return $this->redirectToRoute("member_profile_update");
            }
        }
        return $this->render('admin/profile/update-infos.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('profile/delete', name: "member_profile_delete", methods: ['GET', 'DELETE'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $form = $this->createForm(DeleteFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('yes')->isClicked()) {
                try {
                    if ($user = $this->getUser()) {
//                        $session = $this->requestStack->getSession();
                        $session = new Session();
                        $session->invalidate();
                        $this->security->getToken()->setAuthenticated(false);
                    }

                    $this->em->remove($user);
                    $this->em->flush();

                    // send email
                    $email = (new TemplatedEmail())
                        ->from(new Address($this->getParameter('system_mail'), 'Jeanphi de Medocs.be'))
                        ->to($user->getEmail())
                        ->subject('Suppression de votre compte')
                        ->htmlTemplate('emails/user/delete-confirmation.html.twig')
//            ->textTemplate('emails/list/text-list.html.twig')
                        ->context([
                            'details' => $user
                        ]);

                    $mailer->send($email);

                    return $this->redirectToRoute('public_index');

                } catch (DriverException $ORMException) {
                    dd($ORMException);
                }
            } else return $this->redirectToRoute('member_profile_update');
        }
        return $this->render('admin/profile/delete.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
