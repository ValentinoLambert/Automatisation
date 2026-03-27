<?php

namespace controller\Api;

use model\Api\ApiKey;
use Twig\Environment;

class KeyGenerator
{
    public function show(Environment $twig, array $menu, string $chemin, array $cat): void
    {
        $menu = [
            ['href' => $chemin, 'text' => 'Acceuil'],
            ['href' => $chemin . "/search", 'text' => "Recherche"],
        ];
        $template = $twig->load("Api/key-generator.html.twig");
        echo $template->render(["breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat]);
    }

    public function generateKey(Environment $twig, array $menu, string $chemin, array $cat, string $nom): void
    {
        $menu = [
            ['href' => $chemin, 'text' => 'Acceuil'],
            ['href' => $chemin . "/search", 'text' => "Recherche"],
        ];

        if (str_replace(' ', '', $nom) === '') {
            $template = $twig->load("Api/key-generator-error.html.twig");
            echo $template->render(["breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat]);
            return;
        }

        $key    = uniqid();
        $apikey = new ApiKey();
        $apikey->id_apikey  = $key;
        $apikey->name_key   = htmlentities($nom);
        $apikey->save();

        $template = $twig->load("Api/key-generator-result.html.twig");
        echo $template->render(["breadcrumb" => $menu, "chemin" => $chemin, "categories" => $cat, "key" => $key]);
    }
}
