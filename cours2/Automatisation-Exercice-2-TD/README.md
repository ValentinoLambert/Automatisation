# Annuaire d'entreprises

Application PHP permettant de gérer un annuaire de sociétés avec leurs bureaux et employés.

## Installation

```bash
docker compose up -d
composer install
npm install
```

## Base de données

Créer la base :

```bash
docker compose exec php php bin/console db:create
```

Générer des données aléatoires :

```bash
docker compose exec php php bin/console db:populate
```

## Build assets

Production :

```bash
npm run build
```

Développement (serveur Vite) :

```bash
npm run dev
```

## Accès

Application : http://localhost:8080
