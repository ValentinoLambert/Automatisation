<?php

namespace controller\Categorie;

use model\Categorie\Categorie;
use model\Annonce\Annonce;
use model\Annonce\Photo;
use model\Annonceur\Annonceur;
use Twig\Environment;

class GetCategorie
{
    protected array $categories = [];
    protected array $annonce    = [];

    public function getCategories(): array
    {
        return Categorie::orderBy('nom_categorie')->get()->toArray();
    }

    public function getCategorieContent(string $chemin, int $n): void
    {
        $annoncesList = Annonce::with(['annonceur', 'photo'])
            ->where('id_categorie', "=", $n)
            ->orderBy('id_annonce', 'desc')
            ->get();
            
        $annonces = [];

        foreach ($annoncesList as $annonce) {
            $annonce->url_photo     = $annonce->getMainPhotoUrl($chemin);
            $annonce->nom_annonceur = $annonce->annonceur?->nom_annonceur;
            $annonces[] = $annonce;
        }

        $this->annonce = $annonces;
    }

    public function displayCategorie(Environment $twig, array $menu, string $chemin, array $cat, int $n): void
    {
        $menu = [
            ['href' => $chemin, 'text' => 'Acceuil'],
            ['href' => $chemin . "/cat/" . $n, 'text' => Categorie::find($n)?->nom_categorie],
        ];

        $this->getCategorieContent($chemin, $n);

        $template = $twig->load("Home/index.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "categories" => $cat,
            "annonces"   => $this->annonce,
        ]);
    }
}
