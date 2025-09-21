<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CommentController extends AbstractController
{
    #[Route('/post/{slug}/comment/add', name: 'comment_add')]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, Post $post, EntityManagerInterface $em)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
            $comment->setStatus('pending');
            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', 'Votre commentaire a été ajouté et est en attente de validation.');
            return $this->redirectToRoute('app_post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/comment/{id}/approve', name: 'comment_approve')]
    #[IsGranted('ROLE_MODERATOR')]
    public function approve(Comment $comment, EntityManagerInterface $em)
    {
        $comment->setStatus('approved');
        $em->flush();
        $this->addFlash('success', 'Commentaire approuvé.');
        return $this->redirectToRoute('app_post_show', ['slug' => $comment->getPost()->getSlug()]);
    }

    #[Route('/comment/reply/{id}', name: 'app_comment_reply', methods: ['POST'])]
    public function reply(Post $post, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $comment = new Comment();
        $comment->setContent($data['content'] ?? '');
        $comment->setAuthor($this->getUser());
        $comment->setPost($post);

        if (!empty($data['parent'])) {
            $parent = $em->getRepository(Comment::class)->find($data['parent']);
            if ($parent) {
                $comment->setParent($parent);
            }
        }

        $comment->setStatus('pending'); // en attente de validation
        $comment->setCreatedAt(new \DateTimeImmutable());

        $em->persist($comment);
        $em->flush();

        return $this->json([
            'success' => true,
            'content' => $comment->getContent(),
            'author' => $comment->getAuthor()->getUserIdentifier(),
            'createdAt' => $comment->getCreatedAt()->format('d/m/Y H:i'),
        ]);
    }



}
