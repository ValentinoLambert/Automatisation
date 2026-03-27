<?php

namespace controller\Search;

use model\Annonce\Annonce;
use model\Categorie\Categorie;
use Twig\Environment;

class Search
{
    public function show(Environment $twig, array $menu, string $chemin, array $cat): void
    {
        $menu = [
            ['href' => $chemin, 'text' => 'Acceuil'],
            ['href' => $chemin . "/search", 'text' => "Recherche"],
        ];
        $template = $twig->load("Search/search.html.twig");
        echo $template->render(["breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat]);
    }

    public function research(array $searchParams, Environment $twig, array $menu, string $chemin, array $cat): void
    {
        $menu = [
            ['href' => $chemin, 'text' => 'Acceuil'],
            ['href' => $chemin . "/search", 'text' => "Résultats de la recherche"],
        ];

        $keyword    = str_replace(' ', '', $searchParams['motclef'] ?? '');
        $postalCode = str_replace(' ', '', $searchParams['codepostal'] ?? '');
        $category   = $searchParams['categorie'] ?? '';
        $minPrice   = $searchParams['prix-min'] ?? 'Min';
        $maxPrice   = $searchParams['prix-max'] ?? 'Max';

        $query = Annonce::query();

        if ($keyword !== '') {
            $query->where('description', 'like', '%' . $searchParams['motclef'] . '%');
        }

        if ($postalCode !== '') {
            $query->where('ville', '=', $searchParams['codepostal']);
        }

        if (!in_array($category, ["Toutes catégories", "-----"], true)) {
            $categId = Categorie::where('id_categorie', '=', $category)->first()?->id_categorie;
            if ($categId !== null) {
                $query->where('id_categorie', '=', $categId);
            }
        }

        $minValid = ($minPrice !== "Min");
        $maxValid = ($maxPrice !== "Max" && $maxPrice !== "nolimit");

        if ($minValid && $maxValid) {
            $query->whereBetween('prix', [$minPrice, $maxPrice]);
        } elseif ($minValid) {
            $query->where('prix', '>=', $minPrice);
        } elseif ($maxValid) {
            $query->where('prix', '<=', $maxPrice);
        }

        $annonces = $query->get();

        $template = $twig->load("index.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "annonces"   => $annonces,
            "categories" => $cat
        ]);
    }
}
