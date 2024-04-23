<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Story\TaskStory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        TaskStory::load();
        $user = new User();
        $user->setEmail('theo@test.fr');
        $user->setUsername('theo');
        $user->setPassword(password_hash('pQqMw96K9@ewLAV', PASSWORD_DEFAULT));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $user = new User();
        $user->setEmail('testuser2@test.fr');
        $user->setUsername('test');
        $user->setPassword(password_hash('pQqMw96K9@ewLAV', PASSWORD_DEFAULT));
        $manager->flush();
    }
}
