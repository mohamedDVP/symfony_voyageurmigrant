<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $stats = [];

        // Stats pour modérateur et admin
        if ($this->isGranted('ROLE_MODERATOR') || $this->isGranted('ROLE_ADMIN')) {
            $stats['pendingComments'] = $em->getRepository(Comment::class)
                ->count(['status' => 'pending']);

            $stats['approvedComments'] = $em->getRepository(Comment::class)
                ->count(['status' => 'approved']);
        }

        // Stats uniquement pour admin
        if ($this->isGranted('ROLE_ADMIN')) {
            $stats['totalUsers'] = $em->getRepository(User::class)->count([]);
            $stats['totalAdmins'] = $em->getRepository(User::class)->count(['roles' => ['ROLE_ADMIN']]);
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    #[Route('/profil/edit', name: 'app_edit_profile')]
    #[IsGranted('ROLE_USER')]
    public function editProfile(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Hash password uniquement si un nouveau mot de passe est fourni
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }
}
