<?php

namespace App\Controller\Member\User;

use App\Entity\User;
use App\Form\DeleteFormType;
use App\Form\UserType;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class UserController
 * @package App\Controller\Admin\User
 */
#[Route('/member/')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface      $em,
        private UserPasswordHasherInterface $passwordHasher,
        private Security                    $security,
    )
    {
    }

    #[Route('profile/update', name: "member_profile_update", methods: ['GET', 'PUT'])]
    public function updateInfos(Request $request): Response
    {
        $user = $this->security->getUser();

        $form = $this->createForm(UserType::class, null, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            $plainPassword = $form->getData()['plainPassword'];
            $repeatPassword = $form->getData()['repeatPassword'];
            $oldPassword = $form->getData()['oldPassword'];

            // Mise à jour de l'email
            $user->setEmail($email);

            // Changement de mot de passe (optionnel)
            if (!empty($plainPassword)) {
                // Vérifier que les nouveaux mots de passe correspondent
                if ($plainPassword !== $repeatPassword) {
                    $this->addFlash('error', 'Les deux nouveaux mots de passe ne sont pas identiques');
                    return $this->redirectToRoute("member_profile_update");
                }

                // Vérifier l'ancien mot de passe
                if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
                    $this->addFlash('error', 'Le mot de passe actuel est incorrect');
                    return $this->redirectToRoute("member_profile_update");
                }

                // Tout est OK, on hash et on sauvegarde
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                $this->addFlash('success', 'Mot de passe modifié avec succès');
            }

            // Sauvegarder en base
            $this->em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès');
            return $this->redirectToRoute("member_profile_update");
        }

        return $this->render('member/profile/update.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('profile/{id}/delete', name: "member_profile_delete", methods: ['POST'])]
    public function delete(User $user, Request $request, MailerInterface $mailer): Response
    {
        $submittedToken = $request->getPayload()->get('token');

        if (!$this->isCsrfTokenValid('delete-'.$user->getId(), $submittedToken)) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $this->em->remove($user);
        $this->em->flush();

        // Déconnexion automatique après suppression
        return $this->redirectToRoute('app_logout');
    }
}
