<?php

namespace controller\Home;

use model\Annonce\Annonce;
use model\Annonce\Photo;
use model\Annonceur\Annonceur;
use Twig\Environment;

class Index
{
    public function __construct(
        protected Environment $twig,
        protected string $chemin
    ) {}

    protected array $annonce = [];

    public function displayAllAnnonce(array $menu, array $cat): void
    {
        $menu = [['href' => $this->chemin, 'text' => 'Acceuil']];

        $this->getAll();

        $template = $this->twig->load("Home/index.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $this->chemin,
            "categories" => $cat,
            "annonces"   => $this->annonce,
        ]);
    }

    public function getAll(): void
    {
        $annoncesList = Annonce::with(['annonceur', 'photo'])->orderBy('id_annonce', 'desc')->take(12)->get();
        $annonces     = [];

        foreach ($annoncesList as $annonce) {
            $annonce->url_photo     = $annonce->getMainPhotoUrl();
            $annonce->nom_annonceur = $annonce->annonceur?->nom_annonceur;
            $annonces[] = $annonce;
        }

        $this->annonce = $annonces;
    }

    /**
     * @throws \Exception
     */
    public function displayException(Environment $twig, array $menu, string $chemin, array $cat): never
    {
        throw new \Exception('Cette méthode déclenche une exception.');
    }
}
