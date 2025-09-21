<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $commentReferences = [];

        // On suppose que tu as 50 posts créés dans PostFixtures
        for ($i = 1; $i <= 200; $i++) {
            $comment = new Comment();

            // Sélection aléatoire d'un utilisateur
            $userRef = ['admin_user', 'user', 'moderator_user'][array_rand(['admin_user', 'user', 'moderator_user'])];
            /** @var User $author */
            $author = $this->getReference($userRef, User::class);

            // Sélection aléatoire d’un post
            $randomPostIndex = rand(1, 50);
            /** @var Post $post */
            $post = $this->getReference('post_'.$randomPostIndex, Post::class);

            $comment->setAuthor($author);
            $comment->setPost($post);
            $comment->setContent($faker->paragraph(rand(1, 3), true));
            $comment->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 months', 'now')
            ));

            $manager->persist($comment);

            // Sauvegarder une référence pour créer des réponses plus tard
            $this->addReference('comment_'.$i, $comment);
            $commentReferences[] = 'comment_'.$i;
        }

        // Ajouter des réponses à des commentaires existants
        for ($j = 1; $j <= 100; $j++) {
            $reply = new Comment();

            $userRef = ['admin_user', 'user', 'moderator_user'][array_rand(['admin_user', 'user', 'moderator_user'])];
            $author = $this->getReference($userRef, User::class);

            // Choisir un commentaire parent au hasard
            $parentRef = $commentReferences[array_rand($commentReferences)];
            /** @var Comment $parent */
            $parent = $this->getReference($parentRef, Comment::class);

            $reply->setAuthor($author);
            $reply->setPost($parent->getPost());
            $reply->setParent($parent); // relation parent -> enfant
            $reply->setContent($faker->realText(200, true));
            $reply->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween($parent->getCreatedAt()->format('Y-m-d H:i:s'), 'now')
            ));

            $manager->persist($reply);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PostFixtures::class,
        ];
    }
}
