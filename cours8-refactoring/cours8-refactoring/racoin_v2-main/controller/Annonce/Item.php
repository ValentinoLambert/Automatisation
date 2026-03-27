<?php

declare(strict_types=1);

namespace controller\Annonce;

use model\Annonce\Annonce;
use model\Annonceur\Annonceur;
use model\Departement\Departement;
use model\Annonce\Photo;
use model\Categorie\Categorie;
use Twig\Environment;

class Item
{
    private function isEmail(string $email): bool
    {
        return (bool) preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email);
    }

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

        $nom         = trim($_POST['nom']);
        $email       = trim($_POST['email']);
        $phone       = trim($_POST['phone']);
        $ville       = trim($_POST['ville']);
        $departement = trim($_POST['departement']);
        $categorie   = trim($_POST['categorie']);
        $title       = trim($_POST['title']);
        $description = trim($_POST['description']);
        $price       = trim($_POST['price']);

        $errors = [
            'nameAdvertiser'        => '',
            'emailAdvertiser'       => '',
            'phoneAdvertiser'       => '',
            'villeAdvertiser'       => '',
            'departmentAdvertiser'  => '',
            'categorieAdvertiser'   => '',
            'titleAdvertiser'       => '',
            'descriptionAdvertiser' => '',
            'priceAdvertiser'       => '',
        ];

        if (empty($nom)) {
            $errors['nameAdvertiser'] = 'Veuillez entrer votre nom';
        }
        if (!$this->isEmail($email)) {
            $errors['emailAdvertiser'] = 'Veuillez entrer une adresse mail correcte';
        }
        if (empty($phone) && !is_numeric($phone)) {
            $errors['phoneAdvertiser'] = 'Veuillez entrer votre numéro de téléphone';
        }
        if (empty($ville)) {
            $errors['villeAdvertiser'] = 'Veuillez entrer votre ville';
        }
        if (!is_numeric($departement)) {
            $errors['departmentAdvertiser'] = 'Veuillez choisir un département';
        }
        if (!is_numeric($categorie)) {
            $errors['categorieAdvertiser'] = 'Veuillez choisir une catégorie';
        }
        if (empty($title)) {
            $errors['titleAdvertiser'] = 'Veuillez entrer un titre';
        }
        if (empty($description)) {
            $errors['descriptionAdvertiser'] = 'Veuillez entrer une description';
        }
        if (empty($price) || !is_numeric($price)) {
            $errors['priceAdvertiser'] = 'Veuillez entrer un prix';
        }

        $errors = array_values(array_filter($errors));

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

        $annonceur->email         = htmlentities($allPostVars['email']);
        $annonceur->nom_annonceur = htmlentities($allPostVars['nom']);
        $annonceur->telephone     = htmlentities($allPostVars['phone']);
        $annonce->ville           = htmlentities($allPostVars['ville']);
        $annonce->id_departement  = $allPostVars['departement'];
        $annonce->prix            = htmlentities($allPostVars['price']);
        $annonce->mdp             = password_hash($allPostVars['psw'], PASSWORD_DEFAULT);
        $annonce->titre           = htmlentities($allPostVars['title']);
        $annonce->description     = htmlentities($allPostVars['description']);
        $annonce->id_categorie    = $allPostVars['categorie'];
        $annonce->date            = date('Y-m-d');
        $annonceur->save();
        $annonceur->annonce()->save($annonce);

        $template = $twig->load("Annonce/modif-confirm.html.twig");
        echo $template->render(["breadcrumb" => $menu, "chemin" => $chemin]);
    }
}
