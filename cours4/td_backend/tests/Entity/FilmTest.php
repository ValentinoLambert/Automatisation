<?php

namespace App\Tests\Entity;

use App\Entity\Film;
use App\Entity\Realisateur;
use PHPUnit\Framework\TestCase;

class FilmTest extends TestCase
{
    public function testFilmCanBeCreatedWithBasicProperties(): void
    {
        $film = new Film();
        $film->setTitre("Inception");
        $film->setAnnee(2010);
        $film->setDescription("Un film de science-fiction");

        $this->assertSame("Inception", $film->getTitre());
        $this->assertSame(2010, $film->getAnnee());
        $this->assertSame("Un film de science-fiction", $film->getDescription());
    }
}
