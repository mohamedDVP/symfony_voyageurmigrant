<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_post_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $posts = $em->getRepository(Post::class)->findBy([], ['createdAt' => 'DESC']);
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/{slug}', name: 'app_post_show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Post $post,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
            $comment->setCreatedAt(new \DateTimeImmutable());

            // Vérifier si on répond à un commentaire existant
            $parentId = $form->get('parent')->getData();
            if ($parentId) {
                $parent = $em->getRepository(Comment::class)->find($parentId);
                if ($parent) {
                    $comment->setParent($parent);
                }
            }

            $em->persist($comment);
            $em->flush();

            // ⚡ Si requête AJAX, on renvoie JSON
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'id' => $comment->getId(),
                    'content' => $comment->getContent(),
                    'author' => $comment->getAuthor()->getEmail(),
                    'createdAt' => $comment->getCreatedAt()->format('d/m/Y H:i'),
                    'parentId' => $parentId,
                ]);
            }
            $this->addFlash('info', 'Votre commentaire a été envoyé et est en attente de validation.');
            return $this->redirectToRoute('app_post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentForm' => $form->createView(),
        ]);
    }
}
