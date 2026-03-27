<?php

namespace controller\Annonceur;

use model\Annonce\Annonce;
use model\Annonceur\Annonceur;
use model\Annonce\Photo;
use Twig\Environment;

class ViewAnnonceur
{
    public function afficherAnnonceur(Environment $twig, array $menu, string $chemin, int $n, array $cat): void
    {
        $annonceur = Annonceur::find($n);
        if (!isset($annonceur)) {
            echo "404";
            return;
        }

        $annoncesList = Annonce::with('photo')->where('id_annonceur', '=', $n)->get();
        $annonces     = [];

        foreach ($annoncesList as $annonce) {
            $annonce->url_photo = $annonce->getMainPhotoUrl($chemin);
            $annonces[] = $annonce;
        }

        $template = $twig->load("Annonceur/annonceur.html.twig");
        echo $template->render([
            'nom'        => $annonceur,
            "chemin"     => $chemin,
            "annonces"   => $annonces,
            "categories" => $cat,
        ]);
    }
}
