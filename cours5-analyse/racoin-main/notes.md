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

