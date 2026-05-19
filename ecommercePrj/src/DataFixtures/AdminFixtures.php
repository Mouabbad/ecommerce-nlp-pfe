<?php


namespace App\DataFixtures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class AdminFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
   public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setFirstName('rawane');
        $admin->setLastName('mouabbad');
        $admin->setUsername('admin');
        $admin->setEmail('mouabbadmarwa@gmail.com');
        $admin->setPhone('0711947319');
        $admin->setCity('eljadida');
        $admin->setAddress('jawhara Street 123');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setIsApproved(true);

        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123'); // mot de passe
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['admin'];
    }
}

