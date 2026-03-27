<?php

namespace App\Tests\Entity;

use App\Entity\Realisateur;
use PHPUnit\Framework\TestCase;

class RealisateurTest extends TestCase
{
    public function testRealisateurCanBeCreatedWithBasicProperties(): void
    {
        $realisateur = new Realisateur();
        $realisateur->setNom("Nolan");
        $realisateur->setPrenom("Christopher");
        $realisateur->setAnneeNaissance(1970);

        $this->assertSame("Nolan", $realisateur->getNom());
        $this->assertSame("Christopher", $realisateur->getPrenom());
        $this->assertSame(1970, $realisateur->getAnneeNaissance());
    }
}
