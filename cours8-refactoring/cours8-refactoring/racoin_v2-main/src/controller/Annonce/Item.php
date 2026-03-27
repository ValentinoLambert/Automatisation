<?php

namespace controller\Annonce;

use model\Annonce\Annonce;
use model\Annonceur\Annonceur;
use model\Departement\Departement;
use model\Annonce\Photo;
use model\Categorie\Categorie;
use Twig\Environment;

class Item
{

    public function afficherItem(Environment $twig, array $menu, string $chemin, int $n, array $cat): void
    {
        $annonce = Annonce::find($n);
        if (!isset($annonce)) {
            echo "404";
            return;
        }

        $menu = [
            ['href' => $chemin, 'text' => 'Acceuil'],
            ['href' => $chemin . "/cat/" . $n, 'text' => Categorie::find($annonce->id_categorie)?->nom_categorie],
            ['href' => $chemin . "/item/" . $n, 'text' => $annonce->titre],
        ];

        $annonceur   = Annonceur::find($annonce->id_annonceur);
        $departement = Departement::find($annonce->id_departement);
        $photo       = Photo::where('id_annonce', '=', $n)->get();

        $template = $twig->load("Annonce/item.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "annonce"    => $annonce,
            "annonceur"  => $annonceur,
            "dep"        => $departement?->nom_departement,
            "photo"      => $photo,
            "categories" => $cat,
        ]);
    }

    public function supprimerItemGet(Environment $twig, array $menu, string $chemin, int $n): void
    {
        $annonce = Annonce::find($n);
        if (!isset($annonce)) {
            echo "404";
            return;
        }
        $template = $twig->load("Annonce/delGet.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "annonce"    => $annonce,
        ]);
    }

    public function supprimerItemPost(Environment $twig, array $menu, string $chemin, int $n, array $cat): void
    {
        $annonce = Annonce::find($n);
        $reponse = $annonce !== null && password_verify($_POST["pass"], $annonce->mdp);

        if ($reponse) {
            Photo::where('id_annonce', '=', $n)->delete();
            $annonce->delete();
        }

        $template = $twig->load("Annonce/delPost.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "annonce"    => $annonce,
            "pass"       => $reponse,
            "categories" => $cat,
        ]);
    }

    public function modifyGet(Environment $twig, array $menu, string $chemin, int $id): void
    {
        $annonce = Annonce::find($id);
        if (!isset($annonce)) {
            echo "404";
            return;
        }
        $template = $twig->load("Annonce/modifyGet.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "annonce"    => $annonce,
        ]);
    }

    public function modifyPost(Environment $twig, array $menu, string $chemin, int $n, array $cat, array $dpt): void
    {
        $annonce   = Annonce::find($n);
        $annonceur = Annonceur::find($annonce->id_annonceur);
        $categItem = Categorie::find($annonce->id_categorie)?->nom_categorie;
        $dptItem   = Departement::find($annonce->id_departement)?->nom_departement;
        $reponse   = password_verify($_POST["pass"], $annonce->mdp);

        $template = $twig->load("Annonce/modifyPost.html.twig");
        echo $template->render([
            "breadcrumb"   => $menu,
            "chemin"       => $chemin,
            "annonce"      => $annonce,
            "annonceur"    => $annonceur,
            "pass"         => $reponse,
            "categories"   => $cat,
            "departements" => $dpt,
            "dptItem"      => $dptItem,
            "categItem"    => $categItem,
        ]);
    }

    public function edit(Environment $twig, array $menu, string $chemin, array $allPostVars, int $id): void
    {
        date_default_timezone_set('Europe/Paris');

        $advertiserName = trim((string) ($allPostVars['nom'] ?? ''));
        $email          = trim((string) ($allPostVars['email'] ?? ''));
        $phone          = trim((string) ($allPostVars['phone'] ?? ''));
        $city           = trim((string) ($allPostVars['ville'] ?? ''));
        $departmentId   = trim((string) ($allPostVars['departement'] ?? ''));
        $categoryId     = trim((string) ($allPostVars['categorie'] ?? ''));
        $title          = trim((string) ($allPostVars['title'] ?? ''));
        $description    = trim((string) ($allPostVars['description'] ?? ''));
        $price          = trim((string) ($allPostVars['price'] ?? ''));
        $password       = trim((string) ($allPostVars['psw'] ?? '')); // only for validation, though modifying via edit usually ignores password rewrite unless intended. Left as is to keep same functional logic.

        $errors = [];

        if (empty($advertiserName)) {
            $errors[] = 'Veuillez entrer votre nom';
        }
        if (!\util\Validator::isEmail($email)) {
            $errors[] = 'Veuillez entrer une adresse mail correcte';
        }
        if (empty($phone) && !is_numeric($phone)) {
            $errors[] = 'Veuillez entrer votre numéro de téléphone';
        }
        if (empty($city)) {
            $errors[] = 'Veuillez entrer votre ville';
        }
        if (!is_numeric($departmentId)) {
            $errors[] = 'Veuillez choisir un département';
        }
        if (!is_numeric($categoryId)) {
            $errors[] = 'Veuillez choisir une catégorie';
        }
        if (empty($title)) {
            $errors[] = 'Veuillez entrer un titre';
        }
        if (empty($description)) {
            $errors[] = 'Veuillez entrer une description';
        }
        if (empty($price) || !is_numeric($price)) {
            $errors[] = 'Veuillez entrer un prix';
        }

        if (!empty($errors)) {
            $template = $twig->load("Annonce/add-error.html.twig");
            echo $template->render([
                "breadcrumb" => $menu,
                "chemin"     => $chemin,
                "errors"     => $errors,
            ]);
            return;
        }

        $annonce   = Annonce::find($id);
        $annonceur = Annonceur::find($annonce->id_annonceur);

        $annonceur->email         = htmlentities($email);
        $annonceur->nom_annonceur = htmlentities($advertiserName);
        $annonceur->telephone     = htmlentities($phone);
        $annonce->ville           = htmlentities($city);
        $annonce->id_departement  = (int) $departmentId;
        $annonce->prix            = htmlentities($price);
        // Assuming password rewrite is expected behavior from legacy code
        if (!empty($password)) {
            $annonce->mdp         = password_hash($password, PASSWORD_DEFAULT);
        }
        $annonce->titre           = htmlentities($title);
        $annonce->description     = htmlentities($description);
        $annonce->id_categorie    = (int) $categoryId;
        $annonce->date            = date('Y-m-d');
        
        $annonceur->save();
        $annonceur->annonce()->save($annonce);

        $template = $twig->load("Annonce/modif-confirm.html.twig");
        echo $template->render(["breadcrumb" => $menu, "chemin" => $chemin]);
    }
}
