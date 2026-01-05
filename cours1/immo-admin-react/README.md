# React + Vite - Immo Admin - Frontend

Ce projet est la partie administration du site immobilier, développée avec React et Vite.

## Prérequis

- Docker
- Docker Compose

## Installation et Lancement

Pour lancer le projet localement avec Docker :

1.  **Démarrer les containers** :
    ```bash
    docker compose up -d
    ```
    Cette commande va construire l'image, installer les dépendances (`npm install`) et lancer le serveur de développement (`npm run dev`).

2.  **Accéder au site** :
    Le site est accessible à l'adresse suivante : [http://localhost:3000](http://localhost:3000)

## Commandes utiles

- **Voir les logs** :
    ```bash
    docker logs -f immo-admin-react-node
    ```
- **Redémarrer le projet** :
    ```bash
    docker compose restart
    ```
- **Arrêter le projet** :
    ```bash
    docker compose down
    ```
