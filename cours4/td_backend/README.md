# Application Symfony Films & Réalisateurs

## Installation

Dans le dossier `cours4/td_backend` :

1. Installer les dépendances :

   ```bash
   docker compose run --rm php composer install
   ```

2. Démarrer le projet :
   ```bash
   docker compose up
   ```
   Accès : http://localhost:8080

## Initialisation de la base de données

1. Appliquer la migration :

   ```bash
   export DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

2. Charger les données de démonstration :
   ```bash
   php bin/console doctrine:fixtures:load --no-interaction
   ```

## Accès aux pages principales

- Liste des films : http://localhost:8080/film
- Liste des réalisateurs : http://localhost:8080/realisateur

## Lancer les tests

```bash
./vendor/bin/phpunit --testdox
```

## Vérifier les linters

```bash
./vendor/bin/phpcs
./vendor/bin/phpstan analyse
```

## Problèmes Docker

Si Docker est lent ou pose problème, vous pouvez utiliser PHP localement :

```bash
composer install
php -S 0.0.0.0:8081 -t public
```

Accès : http://localhost:8081
