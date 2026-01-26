<?php

namespace App\DataFixtures;

use App\Entity\Film;
use App\Entity\Realisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Créer 10 réalisateurs
        $realisateurs = [];
        for ($i = 0; $i < 10; $i++) {
            $realisateur = new Realisateur();
            $realisateur->setNom($faker->lastName());
            $realisateur->setPrenom($faker->firstName());
            $realisateur->setAnneeNaissance($faker->numberBetween(1940, 1990));

            $manager->persist($realisateur);
            $realisateurs[] = $realisateur;
        }

        // Créer 30 films avec relation vers les réalisateurs
        for ($i = 0; $i < 30; $i++) {
            $film = new Film();
            $film->setTitre($faker->sentence(3));
            $film->setAnnee($faker->numberBetween(1980, 2024));
            $film->setDescription($faker->paragraph());
            $film->setRealisateur($faker->randomElement($realisateurs));

            $manager->persist($film);
        }

        $manager->flush();
    }
}
