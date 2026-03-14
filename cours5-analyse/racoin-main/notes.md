# Notes – Projet Racoin

## Étape 1 – Analyse théorique

**Langages utilisés**
PHP, SQL, SCSS, JavaScript

**Frameworks principaux**

- Slim 2 (routing)
- Twig 1 (templates)
- Eloquent / illuminate/database 4.2.9 (ORM MySQL)

**But de l'application**
Site de petites annonces : poster, consulter, modifier, supprimer des annonces. Expose aussi une API REST.

**Ce qu'il faut pour démarrer**

- Créer `config/config.ini` avec les identifiants BDD (absent du repo)
- Une base MySQL avec le schéma : `sql/create_schema.sql` + `sql/import_data.sql`
- Le docker-compose n'avait pas de service MySQL → à ajouter

---

## Étape 2 – Prise en main & démarrage

**Actions réalisées**

1. Créé `config/config.ini` avec les identifiants correspondant au service Docker (`host=mysql`, `database=racoin`, `username=racoin`, `password=racoin`).
2. Ajouté un service `mysql:8.0` dans `docker-compose.yml` :
   - initialise automatiquement la BDD via les fichiers `sql/` montés dans `/docker-entrypoint-initdb.d/`
   - le service PHP `depends_on: mysql` pour démarrer après la BDD

**Mode d'emploi**

```bash
# Démarrer l'application
docker compose up -d --build

# L'app est accessible sur http://localhost:8080

# Arrêter
docker compose down
```

---

## Étape 3 – Préparer la maintenance

### Versions obsolètes

| Composant                      | Version utilisée | Dernière version | Statut                                        |
| ------------------------------ | ---------------- | ---------------- | --------------------------------------------- |
| PHP                            | 7.4              | 8.3              | plus maintenue depuis nov. 2022               |
| Slim                           | 2.6.3            | 4.15.1           | plus maintenue, 2 versions majeures de retard |
| Twig                           | 1.44.8           | 3.11.3           | plus maintenue, 2 versions majeures de retard |
| illuminate/database (Eloquent) | 4.2.9            | 8.x              | plus maintenue, très ancienne                 |
| nesbot/carbon                  | 1.39.1           | 2.73.0           | version majeure de retard                     |
| MySQL (image Docker)           | 8.0              | 8.4 LTS          | pas critique mais à surveiller                |

### Todo list – améliorations maintenance

| #   | Amélioration                                              | Temps /10 | Impact /10                                             |
| --- | --------------------------------------------------------- | --------- | ------------------------------------------------------ |
| 1   | Passer PHP 7.4 → 8.2                                      | 5         | 10 – sécurité critique, PHP 7.4 n'a plus de correctifs |
| 2   | Mettre à jour Slim 2 → Slim 4                             | 7         | 8 – routeur central, failles de sécu                   |
| 3   | Mettre à jour Twig 1 → Twig 3                             | 6         | 7 – moteur de templates                                |
| 4   | Mettre à jour Eloquent 4 → 8                              | 7         | 7 – ORM utilisé partout, correctifs de sécurité        |
| 5   | Remplacer config.ini par variables d'environnement Docker | 2         | 7 – évite les credentials en clair                     |
| 6   | Ajouter phpstan/phpcs en CI                               | 4         | 6 – détecte les erreurs tôt                            |
| 7   | Ajouter des tests unitaires (PHPUnit)                     | 8         | 7 – aucune couverture de tests actuellement            |

---

## Étape 4 – Réaliser la maintenance

1. **PHP 7.4 → 8.2** : mise à jour dans le Dockerfile (`FROM php:8.2-cli`)
2. **Twig 1 → 3** : `composer.json` `~1.0` → `^3.0`, namespaces mis à jour dans les controllers (`\Twig\Loader\FilesystemLoader`, `\Twig\Environment`, `load()` à la place de `loadTemplate()`)
3. **Eloquent 4 → 8** : `composer.json` `4.2.9` → `^8.0`
4. **Carbon 1 → 2** : mis à jour automatiquement
5. `composer update` lancé dans le conteneur (`--user root` nécessaire pour les droits d'écriture sur le volume)
6. App vérifiée HTTP 200 après les mises à jour

**Slim 2 non migré à cette étape** : Slim 4 nécessite une réécriture de toutes les routes → traité en étape 5.

---

## Étape 5 – Amélioration continue

### Migration Slim 2 → Slim 4

**Pourquoi ?**
Slim 2 était la seule dépendance majeure non mise à jour. Cotée 8/10 dans la todo list. Tout le code Slim est concentré dans un seul fichier (`index.php`, 232 lignes), donc rapport effort/impact favorable.

**Ce qui a changé dans `index.php` :**

- `new \Slim\Slim()` → `AppFactory::create()`
- Paramètres de route `:id` → `{id}` + `$args['id']`
- `$app->request->post()` → `$request->getParsedBody()`
- `$app->response->headers->set(...)` → `$response->withHeader(...)`
- `->via('GET', 'POST')` → `$app->map(['GET', 'POST'], ...)`
- `$app->notFound()` → `throw new HttpNotFoundException($request)`
- Chaque callback reçoit `($request, $response, $args)` et retourne `$response`

**Résultat :** app fonctionnelle sur Slim 4.15.1 + PHP 8.2, HTTP 200 sur toutes les routes testées
