<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use controller\Home\Index;
use controller\Annonce\AddItem;
use controller\Annonce\Item;
use controller\Search\Search;
use controller\Categorie\GetCategorie;
use controller\Departement\GetDepartment;
use controller\Annonceur\ViewAnnonceur;
use controller\Api\KeyGenerator;
use db\connection;

use model\Annonce\Annonce;
use model\Categorie\Categorie;
use model\Annonceur\Annonceur;
use model\Departement\Departement;
use Slim\Factory\AppFactory;
use Slim\Http\ServerRequest as Request;
use Slim\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


connection::createConn();

// Initialisation de Slim 4
$app = AppFactory::create();

// Support du parsing du corps des requêtes (JSON, form-data, etc.)
$app->addBodyParsingMiddleware();

// Initialisation de Twig
$loader = new FilesystemLoader(__DIR__ . '/template');
$twig   = new Environment($loader);

// Middleware : suppression du trailing slash (Signature Slim 4)
$app->add(function (Request $request, RequestHandler $handler) {
    $uri  = $request->getUri();
    $path = $uri->getPath();
    if ($path !== '/' && str_ends_with($path, '/')) {
        $uri = $uri->withPath(substr($path, 0, -1));
        if ($request->getMethod() === 'GET') {
            $response = new \Slim\Psr7\Response();
            return $response
                ->withHeader('Location', (string) $uri)
                ->withStatus(301);
        }
        $request = $request->withUri($uri);
    }
    return $handler->handle($request);
});

// Middleware de routage
$app->addRoutingMiddleware();

// Middleware d'erreurs
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

if (!isset($_SESSION)) {
    session_start();
    $_SESSION['formStarted'] = true;
}

if (!isset($_SESSION['token'])) {
    $token                  = md5(uniqid((string) rand(), true));
    $_SESSION['token']      = $token;
    $_SESSION['token_time'] = time();
} else {
    $token = $_SESSION['token'];
}

$menu   = [['href' => './index.php', 'text' => 'Accueil']];
$chemin = dirname($_SERVER['SCRIPT_NAME']);

$cat = new GetCategorie();
$dpt = new GetDepartment();

$app->get('/', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat): Response {
    (new Index($twig, $chemin))->displayAllAnnonce($menu, $cat->getCategories());
    return $response;
});

$app->get('/exception', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat): Response {
    (new Index($twig, $chemin))->displayException($twig, $menu, $chemin, $cat->getCategories());
    return $response;
});

$app->get('/item/{n}', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin, $cat): Response {
    (new Item())->afficherItem($twig, $menu, $chemin, (int) $arg['n'], $cat->getCategories());
    return $response;
});

$app->get('/add', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat, $dpt): Response {
    (new AddItem())->addItemView($twig, $menu, $chemin, $cat->getCategories(), $dpt->getAllDepartments());
    return $response;
});

$app->post('/add', function (Request $request, Response $response) use ($twig, $menu, $chemin): Response {
    (new AddItem())->addNewItem($twig, $menu, $chemin, (array) $request->getParsedBody());
    return $response;
});

$app->get('/item/{id}/edit', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin): Response {
    (new Item())->modifyGet($twig, $menu, $chemin, (int) $arg['id']);
    return $response;
});

$app->post('/item/{id}/edit', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin, $cat, $dpt): Response {
    (new Item())->modifyPost($twig, $menu, $chemin, (int) $arg['id'], $cat->getCategories(), $dpt->getAllDepartments());
    return $response;
});

$app->map(['GET', 'POST'], '/item/{id}/confirm', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin): Response {
    (new Item())->edit($twig, $menu, $chemin, (array) $request->getParsedBody(), (int) $arg['id']);
    return $response;
});

$app->get('/search', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat): Response {
    (new Search())->show($twig, $menu, $chemin, $cat->getCategories());
    return $response;
});

$app->post('/search', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat): Response {
    (new Search())->research((array) $request->getParsedBody(), $twig, $menu, $chemin, $cat->getCategories());
    return $response;
});

$app->get('/annonceur/{n}', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin, $cat): Response {
    (new ViewAnnonceur())->afficherAnnonceur($twig, $menu, $chemin, (int) $arg['n'], $cat->getCategories());
    return $response;
});

$app->get('/del/{n}', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin): Response {
    (new Item())->supprimerItemGet($twig, $menu, $chemin, (int) $arg['n']);
    return $response;
});

$app->post('/del/{n}', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin, $cat): Response {
    (new Item())->supprimerItemPost($twig, $menu, $chemin, (int) $arg['n'], $cat->getCategories());
    return $response;
});

$app->get('/cat/{n}', function (Request $request, Response $response, array $arg) use ($twig, $menu, $chemin, $cat): Response {
    (new GetCategorie())->displayCategorie($twig, $menu, $chemin, $cat->getCategories(), (int) $arg['n']);
    return $response;
});

$app->get('/api[/]', function (Request $request, Response $response) use ($chemin): Response {
    $apiMenu = [
        ['href' => $chemin, 'text' => 'Acceuil'],
        ['href' => $chemin . '/api', 'text' => 'Api'],
    ];
    $template = \Twig\Environment::class; // Placeholder to avoid closure use ($twig) if not passed correctly, but we have $twig in scope.
    // Wait, I should use the $twig from outer scope.
    global $twig;
    $template = $twig->load('Api/api.html.twig');
    $response->getBody()->write($template->render(['breadcrumb' => $apiMenu, 'chemin' => $chemin]));
    return $response;
});

$app->group('/api', function (\Slim\Routing\RouteCollectorProxy $group) use ($twig, $menu, $chemin, $cat): void {

    $group->group('/annonce', function (\Slim\Routing\RouteCollectorProxy $group): void {

        $group->get('/{id}', function (Request $request, Response $response, array $arg): Response {
            $id          = (int) $arg['id'];
            $annonceList = ['id_annonce', 'id_categorie as categorie', 'id_annonceur as annonceur', 'id_departement as departement', 'prix', 'date', 'titre', 'description', 'ville'];
            $return      = Annonce::select($annonceList)->find($id);

            if ($return === null) {
                return $response->withStatus(404);
            }

            $return->categorie   = Categorie::find($return->categorie);
            $return->annonceur   = Annonceur::select('email', 'nom_annonceur', 'telephone')->find($return->annonceur);
            $return->departement = Departement::select('id_departement', 'nom_departement')->find($return->departement);
            $return->links       = ['self' => ['href' => '/api/annonce/' . $return->id_annonce]];

            return $response->withHeader('Content-Type', 'application/json')->withJson($return);
        });
    });

    $group->group('/annonces[/]', function (\Slim\Routing\RouteCollectorProxy $group): void {

        $group->get('', function (Request $request, Response $response): Response {
            $annonceList = ['id_annonce', 'prix', 'titre', 'ville'];
            $annonces    = Annonce::all($annonceList);

            foreach ($annonces as $ann) {
                $ann->links = ['self' => ['href' => '/api/annonce/' . $ann->id_annonce]];
            }
            $annonces->links = ['self' => ['href' => '/api/annonces/']];

            return $response->withHeader('Content-Type', 'application/json')->withJson($annonces);
        });
    });

    $group->group('/categorie', function (\Slim\Routing\RouteCollectorProxy $group): void {

        $group->get('/{id}', function (Request $request, Response $response, array $arg): Response {
            $id       = (int) $arg['id'];
            $annonces = Annonce::select('id_annonce', 'prix', 'titre', 'ville')
                ->where('id_categorie', '=', $id)
                ->get();

            foreach ($annonces as $ann) {
                $ann->links = ['self' => ['href' => '/api/annonce/' . $ann->id_annonce]];
            }

            $categorie        = Categorie::find($id);
            if ($categorie === null) return $response->withStatus(404);
            $categorie->links = ['self' => ['href' => '/api/categorie/' . $id]];
            $categorie->annonces = $annonces;

            return $response->withHeader('Content-Type', 'application/json')->withJson($categorie);
        });
    });

    $group->group('/categories[/]', function (\Slim\Routing\RouteCollectorProxy $group): void {

        $group->get('', function (Request $request, Response $response): Response {
            $categories = Categorie::get();

            foreach ($categories as $cat) {
                $cat->links = ['self' => ['href' => '/api/categorie/' . $cat->id_categorie]];
            }
            $categories->links = ['self' => ['href' => '/api/categories/']];

            return $response->withHeader('Content-Type', 'application/json')->withJson($categories);
        });
    });

    $group->get('/key', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat): Response {
        (new KeyGenerator())->show($twig, $menu, $chemin, $cat->getCategories());
        return $response;
    });

    $group->post('/key', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat): Response {
        (new KeyGenerator())->generateKey($twig, $menu, $chemin, $cat->getCategories(), (string) ($request->getParsedBody()['nom'] ?? ''));
        return $response;
    });
});


$app->run();
