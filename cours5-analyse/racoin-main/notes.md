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
