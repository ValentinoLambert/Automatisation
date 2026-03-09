<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require 'vendor/autoload.php';
use db\connection;

use Illuminate\Database\Query\Expression as raw;
use model\Annonce;
use model\Categorie;
use model\Annonceur;
use model\Departement;


connection::createConn();

$app = \Slim\Factory\AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

if (!isset($_SESSION)) {
    session_start();
    $_SESSION['formStarted'] = true;
}

if (!isset($_SESSION['token'])) {
    $token = md5(uniqid(rand(), TRUE));
    $_SESSION['token'] = $token;
    $_SESSION['token_time'] = time();
} else {
    $token = $_SESSION['token'];
}

$loader = new \Twig\Loader\FilesystemLoader('template');
$twig = new \Twig\Environment($loader);

$menu = array(
    array('href' => "./index.php",
        'text' => 'Accueil')
);

$chemin = dirname($_SERVER['SCRIPT_NAME']);

$cat = new \controller\getCategorie();
$dpt = new \controller\getDepartment();

$app->get('/', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
    $index = new \controller\index();
    $index->displayAllAnnonce($twig, $menu, $chemin, $cat->getCategories());
    return $response;
});

$app->get('/item/{n}', function ($request, $response, $args) use ($twig, $menu, $chemin, $cat) {
    $item = new \controller\item();
    $item->afficherItem($twig, $menu, $chemin, $args['n'], $cat->getCategories());
    return $response;
});

$app->get('/add/', function ($request, $response) use ($twig, $menu, $chemin, $cat, $dpt) {
    $ajout = new controller\addItem();
    $ajout->addItemView($twig, $menu, $chemin, $cat->getCategories(), $dpt->getAllDepartments());
    return $response;
});

$app->post('/add/', function ($request, $response) use ($twig, $menu, $chemin) {
    $allPostVars = $request->getParsedBody();
    $ajout = new controller\addItem();
    $ajout->addNewItem($twig, $menu, $chemin, $allPostVars);
    return $response;
});

$app->get('/item/{id}/edit', function ($request, $response, $args) use ($twig, $menu, $chemin) {
    $item = new \controller\item();
    $item->modifyGet($twig, $menu, $chemin, $args['id']);
    return $response;
});

$app->post('/item/{id}/edit', function ($request, $response, $args) use ($twig, $menu, $chemin, $cat, $dpt) {
    $allPostVars = $request->getParsedBody();
    $item = new \controller\item();
    $item->modifyPost($twig, $menu, $chemin, $args['id'], $allPostVars, $cat->getCategories(), $dpt->getAllDepartments());
    return $response;
});

$app->map(['GET', 'POST'], '/item/{id}/confirm', function ($request, $response, $args) use ($twig, $menu, $chemin) {
    $allPostVars = $request->getParsedBody();
    $item = new \controller\item();
    $item->edit($twig, $menu, $chemin, $args['id'], $allPostVars);
    return $response;
});

$app->get('/search/', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
    $s = new controller\Search();
    $s->show($twig, $menu, $chemin, $cat->getCategories());
    return $response;
});

$app->post('/search/', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
    $array = $request->getParsedBody();
    $s = new controller\Search();
    $s->research($array, $twig, $menu, $chemin, $cat->getCategories());
    return $response;
});

$app->get('/annonceur/{n}', function ($request, $response, $args) use ($twig, $menu, $chemin, $cat) {
    $annonceur = new controller\viewAnnonceur();
    $annonceur->afficherAnnonceur($twig, $menu, $chemin, $args['n'], $cat->getCategories());
    return $response;
});

$app->get('/del/{n}', function ($request, $response, $args) use ($twig, $menu, $chemin) {
    $item = new controller\item();
    $item->supprimerItemGet($twig, $menu, $chemin, $args['n']);
    return $response;
});

$app->post('/del/{n}', function ($request, $response, $args) use ($twig, $menu, $chemin, $cat) {
    $item = new controller\item();
    $item->supprimerItemPost($twig, $menu, $chemin, $args['n'], $cat->getCategories());
    return $response;
});

$app->get('/cat/{n}', function ($request, $response, $args) use ($twig, $menu, $chemin, $cat) {
    $categorie = new controller\getCategorie();
    $categorie->displayCategorie($twig, $menu, $chemin, $cat->getCategories(), $args['n']);
    return $response;
});

$app->get('/api[/]', function ($request, $response) use ($twig, $menu, $chemin) {
    $template = $twig->load("api.html.twig");
    $menu = array(
        array('href' => $chemin, 'text' => 'Acceuil'),
        array('href' => $chemin . '/api', 'text' => 'Api')
    );
    echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin));
    return $response;
});

$app->group('/api', function (\Slim\Routing\RouteCollectorProxy $group) use ($twig, $menu, $chemin, $cat) {

    $group->group('/annonce', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->get('/{id}', function ($request, $response, $args) {
            $annonceList = ['id_annonce', 'id_categorie as categorie', 'id_annonceur as annonceur', 'id_departement as departement', 'prix', 'date', 'titre', 'description', 'ville'];
            $return = Annonce::select($annonceList)->find($args['id']);

            if (isset($return)) {
                $return->categorie = Categorie::find($return->categorie);
                $return->annonceur = Annonceur::select('email', 'nom_annonceur', 'telephone')->find($return->annonceur);
                $return->departement = Departement::select('id_departement', 'nom_departement')->find($return->departement);
                $links = [];
                $links["self"]["href"] = "/api/annonce/" . $return->id_annonce;
                $return->links = $links;
                $response->getBody()->write($return->toJson());
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                throw new \Slim\Exception\HttpNotFoundException($request);
            }
        });
    });

    $group->group('/annonces', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->get('[/]', function ($request, $response) {
            $annonceList = ['id_annonce', 'prix', 'titre', 'ville'];
            $a = Annonce::all($annonceList);
            $links = [];
            foreach ($a as $ann) {
                $links["self"]["href"] = "/api/annonce/" . $ann->id_annonce;
                $ann->links = $links;
            }
            $links["self"]["href"] = "/api/annonces/";
            $a->links = $links;
            $response->getBody()->write($a->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $group->group('/categorie', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->get('/{id}', function ($request, $response, $args) {
            $a = Annonce::select('id_annonce', 'prix', 'titre', 'ville')
                ->where("id_categorie", "=", $args['id'])
                ->get();
            $links = [];
            foreach ($a as $ann) {
                $links["self"]["href"] = "/api/annonce/" . $ann->id_annonce;
                $ann->links = $links;
            }
            $c = Categorie::find($args['id']);
            $links["self"]["href"] = "/api/categorie/" . $args['id'];
            $c->links = $links;
            $c->annonces = $a;
            $response->getBody()->write($c->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $group->group('/categories', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->get('[/]', function ($request, $response) {
            $c = Categorie::get();
            $links = [];
            foreach ($c as $cat) {
                $links["self"]["href"] = "/api/categorie/" . $cat->id_categorie;
                $cat->links = $links;
            }
            $links["self"]["href"] = "/api/categories/";
            $c->links = $links;
            $response->getBody()->write($c->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $group->get('/key', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
        $kg = new controller\KeyGenerator();
        $kg->show($twig, $menu, $chemin, $cat->getCategories());
        return $response;
    });

    $group->post('/key', function ($request, $response) use ($twig, $menu, $chemin, $cat) {
        $nom = $request->getParsedBody()['nom'];
        $kg = new controller\KeyGenerator();
        $kg->generateKey($twig, $menu, $chemin, $cat->getCategories(), $nom);
        return $response;
    });
});

$app->run();
