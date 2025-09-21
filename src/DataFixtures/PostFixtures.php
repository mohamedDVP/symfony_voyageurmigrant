<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Récupérer l'utilisateur admin
        /** @var User $adminReference */
        $userRefs = ['admin_user', 'user', 'moderator_user']; // Ajouter d'autres rôles si nécessaire

        // Nombre exact de catégories créées dans CategoryFixtures
        $totalCategories = 197; // 194 pays + 12 infos pratiques (ajuste selon ta liste)

        // Créer 50 posts d'exemple
        for ($i = 1; $i <= 50; $i++) {
            $post = new Post();
            $title = $faker->realText(100, true);
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

            $post->setTitle($title);
            $post->setSlug($slug);
            $post->setContent($faker->realText(9999, true));
            $randomUserRef = $userRefs[array_rand($userRefs)];
            $post->setAuthor($this->getReference($randomUserRef, User::class));
            $post->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));

            // Ajouter 1 à 3 catégories aléatoires
            $categoryCount = rand(1, 3);
            $added = [];
            for ($j = 0; $j < $categoryCount; $j++) {
                do {
                    $categoryIndex = rand(0, $totalCategories - 1);
                } while (in_array($categoryIndex, $added));

                $added[] = $categoryIndex;
                $post->addCategory($this->getReference('category_'.$categoryIndex, Category::class));
            }

            $manager->persist($post);

            // Référence du post pour d'autres fixtures (ex: commentaires)
            $this->addReference('post_'.$i, $post);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,       // Pour l'auteur
            CategoryFixtures::class,   // Pour toutes les catégories
        ];
    }
}
