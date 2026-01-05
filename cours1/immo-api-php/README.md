# API Immobilier (immo-api-php)

API REST développée avec Slim Framework v4, PHP 8.2 et MySQL/MariaDB.

## Description du projet

Cette API permet de gérer des données immobilières. Elle utilise:

- **Slim Framework 4** : Framework PHP minimaliste pour créer des APIs REST
- **PHP 8.2** : Langage de programmation
- **MariaDB 10** : Base de données relationnelle
- **Nginx** : Serveur web
- **Docker** : Conteneurisation de l'application

## Prérequis

- Docker
- Docker Compose

## Installation

### 1. Cloner le projet

```bash
git clone <url-du-repo>
cd immo-api-php
```

### 2. Configurer les variables d'environnement

Créer un fichier `.env` à partir du fichier `.env.exemple`:

```bash
cp .env.exemple .env
```

Les valeurs par défaut dans `.env.exemple` sont déjà configurées et fonctionnelles. Vous pouvez les modifier si nécessaire.

### 3. Démarrer les conteneurs Docker

```bash
docker compose up -d --build
```

Cette commande va:

- Construire l'image PHP avec toutes les extensions nécessaires
- Démarrer le serveur Nginx (accessible sur le port 8080)
- Démarrer la base de données MariaDB (accessible sur le port 3307)
- Démarrer PHP-FPM

### 4. Installer les dépendances PHP

```bash
docker compose exec php composer install
```

### 5. Vérifier que l'application fonctionne

```bash
curl http://localhost:8080
```

Vous devriez voir la réponse:

```json
{ "message": "Hello World!" }
```

## Commandes utiles

### Arrêter les conteneurs

```bash
docker compose stop
```

### Arrêter et supprimer les conteneurs

```bash
docker compose down
```

### Voir les logs d'un conteneur

```bash
docker compose logs php
docker compose logs server
docker compose logs database
```

### Accéder au conteneur PHP

```bash
docker compose exec php sh
```

### Installer une nouvelle dépendance

```bash
docker compose exec php composer require nom-du-package
```

## Structure du projet

```
immo-api-php/
├── docker/                 # Configuration Docker
│   ├── nginx/             # Configuration Nginx
│   └── php/               # Dockerfile PHP
├── public/                # Point d'entrée de l'application
│   └── index.php
├── src/                   # Code source de l'application
│   ├── Controllers/       # Contrôleurs
│   ├── Middlewares/       # Middlewares
│   ├── Models/            # Modèles
│   ├── Routes/            # Définition des routes
│   ├── Utils/             # Utilitaires
│   └── config/            # Configuration
├── .env                   # Variables d'environnement (non versionné)
├── .env.exemple           # Exemple de variables d'environnement
├── composer.json          # Dépendances PHP
├── docker-compose.yml     # Configuration Docker Compose
└── README.md             # Ce fichier
```

## Stack technique

- **PHP 8.2** (Alpine Linux)
- **Nginx** (Alpine Linux)
- **MariaDB 10**
- **Composer** pour la gestion des dépendances

### Dépendances principales

- `slim/slim` : Framework PHP
- `illuminate/database` : ORM Eloquent
- `firebase/php-jwt` : Gestion des tokens JWT
- `vlucas/phpdotenv` : Gestion des variables d'environnement
- `respect/validation` : Validation des données
- `lukasoppermann/http-status` : Codes de statut HTTP

## Développement

Le projet utilise le workflow Gitflow:

- Branche `main` : Production
- Branche `develop` : Développement
- Branches `feature/*` : Nouvelles fonctionnalités
- Branches `hotfix/*` : Corrections urgentes

Chaque modification doit être faite via une branche `feature/*` et mergée sur `develop` via une pull request.
