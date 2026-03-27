<?php

namespace controller\Annonce;

use model\Annonce\Annonce;
use model\Annonceur\Annonceur;
use Twig\Environment;

class AddItem
{
    public function addItemView(Environment $twig, array $menu, string $chemin, array $cat, array $dpt): void
    {
        $template = $twig->load("Annonce/add.html.twig");
        echo $template->render([
            "breadcrumb"   => $menu,
            "chemin"       => $chemin,
            "categories"   => $cat,
            "departements" => $dpt,
        ]);
    }

    public function addNewItem(Environment $twig, array $menu, string $chemin, array $allPostVars): void
    {
        date_default_timezone_set('Europe/Paris');

        $advertiserName  = trim((string) ($allPostVars['nom'] ?? ''));
        $email           = trim((string) ($allPostVars['email'] ?? ''));
        $phone           = trim((string) ($allPostVars['phone'] ?? ''));
        $city            = trim((string) ($allPostVars['ville'] ?? ''));
        $departmentId    = trim((string) ($allPostVars['departement'] ?? ''));
        $categoryId      = trim((string) ($allPostVars['categorie'] ?? ''));
        $title           = trim((string) ($allPostVars['title'] ?? ''));
        $description     = trim((string) ($allPostVars['description'] ?? ''));
        $price           = trim((string) ($allPostVars['price'] ?? ''));
        $password        = trim((string) ($allPostVars['psw'] ?? ''));
        $passwordConfirm = trim((string) ($allPostVars['confirm-psw'] ?? ''));

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
        if (empty($password) || empty($passwordConfirm) || $password !== $passwordConfirm) {
            $errors[] = 'Les mots de passes ne sont pas identiques';
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

        $annonceur = new Annonceur();
        $annonceur->email         = htmlentities($email);
        $annonceur->nom_annonceur = htmlentities($advertiserName);
        $annonceur->telephone     = htmlentities($phone);
        $annonceur->save();

        $annonce = new Annonce();
        $annonce->ville          = htmlentities($city);
        $annonce->id_departement = (int) $departmentId;
        $annonce->prix           = htmlentities($price);
        $annonce->mdp            = password_hash($password, PASSWORD_DEFAULT);
        $annonce->titre          = htmlentities($title);
        $annonce->description    = htmlentities($description);
        $annonce->id_categorie   = (int) $categoryId;
        $annonce->date           = date('Y-m-d');
        $annonceur->annonce()->save($annonce);

        $template = $twig->load("Annonce/add-confirm.html.twig");
        echo $template->render(["breadcrumb" => $menu, "chemin" => $chemin]);
    }
}
