<?php


// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
       
        // src/DataFixtures/AppFixtures.php
         $produit = new Produit();
         $produit->setNom("T-shirt");
         $produit->setDescription("Un joli t-shirt");
         $produit->setPrix(120.50);
         $produit->setImage("uploads/produits/joli-t-shert.jpg");
         $produit->setStock(20);
         $produit->setCreatedAt(new \DateTimeImmutable());
         $manager->persist($produit);

          // Produit 2
    $produit2 = new Produit();
    $produit2->setNom("robe de filles");
    $produit2->setDescription("une reboe vert");
    $produit2->setPrix(250.00);
    $produit2->setImage("uploads/produits/robe.jpg");
    $produit2->setStock(15);
    $produit2->setCreatedAt(new \DateTimeImmutable());
    $manager->persist($produit2);

        $manager->flush();
    }
    

    public static function getGroups(): array
    {
        return ['produits'];
    }
}

