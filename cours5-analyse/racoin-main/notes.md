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

## Étape 3 – Préparer la maintenance

### Versions obsolètes

| Composant | Version utilisée | Dernière version | Statut |
|-----------|-----------------|-----------------|--------|
| PHP | 7.4 | 8.3 | ❌ EOL depuis nov. 2022 |
| Slim | 2.6.3 | 4.15.1 | ❌ EOL, 2 versions majeures de retard |
| Twig | 1.44.8 | 3.11.3 | ❌ EOL, 2 versions majeures de retard |
| illuminate/database (Eloquent) | 4.2.9 | 8.x | ❌ EOL, très ancien |
| nesbot/carbon | 1.39.1 | 2.73.0 | ❌ version majeure de retard |
| MySQL (image Docker) | 8.0 | 8.4 LTS | ⚠️ pas critique mais à surveiller |

### Todo list – améliorations maintenance

| # | Amélioration | Temps /10 | Impact /10 |
|---|-------------|-----------|-----------|
| 1 | Passer PHP 7.4 → 8.2 (LTS supporté) | 5 | 10 – sécurité critique, PHP 7.4 n'a plus de correctifs |
| 2 | Mettre à jour Slim 2 → Slim 4 | 7 | 8 – routeur central, failles connues sur Slim 2 |
| 3 | Mettre à jour Twig 1 → Twig 3 | 6 | 7 – moteur de templates, XSS potentiel |
| 4 | Mettre à jour Eloquent 4 → 8 | 7 | 7 – ORM utilisé partout, correctifs de sécurité |
| 5 | Remplacer config.ini par variables d'environnement Docker | 2 | 7 – évite les credentials en clair hors gitignore |
| 6 | Ajouter phpstan/phpcs en CI | 4 | 6 – détecte les erreurs tôt, améliore la qualité |
| 7 | Ajouter des tests unitaires (PHPUnit) | 8 | 7 – aucune couverture de tests actuellement |
