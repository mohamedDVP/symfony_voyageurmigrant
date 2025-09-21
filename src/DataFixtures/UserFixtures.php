<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Admin
        $admin = new User();
        $admin->setEmail('admin@voyageurmigrant.com');
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'adminpass'));
        $manager->persist($admin);
        $this->addReference('admin_user', $admin);

        // Modérateur
        $mod = new User();
        $mod->setEmail('moderator@voyageurmigrant.com');
        $mod->setUsername('moderator');
        $mod->setRoles(['ROLE_MODERATOR']);
        $mod->setPassword($this->hasher->hashPassword($mod, 'modpass'));
        $manager->persist($mod);
        $this->addReference('moderator_user', $mod);

        // Utilisateur classique
        $user = new User();
        $user->setEmail('user@mail.com');
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, 'userpass'));
        $manager->persist($user);
        $this->addReference('user', $user);

        $manager->flush();
    }
}
