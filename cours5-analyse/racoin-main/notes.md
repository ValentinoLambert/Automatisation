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

- `composer install` (vendor/ absent)
- Créer `config/config.ini` avec les identifiants BDD (absent)
- Une base MySQL avec le schéma : `sql/create_schema.sql` + `sql/import_data.sql`
- Le docker-compose n'a pas de service MySQL → à ajouter

---

## Étape 2 – Prise en main & démarrage

**Actions réalisées**

1. Créé `config/config.ini` avec les identifiants correspondant au service Docker (`host=mysql`, `database=racoin`, `username=racoin`, `password=racoin`).

2. Ajouté un service `mysql:8.0` dans `docker-compose.yml` :
   - initialise automatiquement la BDD via les fichiers dans `sql/` montés dans `/docker-entrypoint-initdb.d/`
   - les credentials correspondent à `config.ini`
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

## Étape 4 – Actions réalisées

1. **Montée de version PHP** : Passage de PHP 7.4 à **PHP 8.2-cli** dans le Dockerfile.
2. **Modernisation des dépendances** (via `composer.json`) :
   - **Twig 1.x → 3.x** : Moteur de template à jour.
   - **Eloquent (illuminate/database) 4.2 → 8.x** : ORM compatible PHP 8.
   - **Carbon 1.x → 2.x** : Gestion des dates mise à jour.
3. **Adaptation du code** :
   - Mise à jour des namespaces Twig (`\Twig\Loader\FilesystemLoader`, `\Twig\Environment`).
   - Remplacement de `loadTemplate()` par `load()` dans tous les contrôleurs.
   - Ajout d'une gestion d'erreurs au début de `index.php` pour ignorer les notices de dépréciation de Slim 2 (indispensable pour PHP 8.2 sans réécrire le framework).
4. **Optimisation Docker** :
   - Ajout du service `mysql:8.0` au `docker-compose.yml`.
   - Automatisation du `composer install` au démarrage.
   - Suppression de l'attribut `version` obsolète.

---

## Étape 5 (bonus)

*(à compléter si besoin)*

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
| 6   | Ajouter phpstan/phpcs en CI                               | 4         | 6 – détecte les erreurs tôt, améliore la qualité       |
| 7   | Ajouter des tests unitaires (PHPUnit)                     | 8         | 7 – aucune couverture de tests actuellement            |

.
