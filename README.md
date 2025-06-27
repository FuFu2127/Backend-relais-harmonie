# Relais d'Harmonie

Projet backend avec Symfony pour le site web Relais d'Harmonie.

## Prérequis

- PHP >= 8.x
- Composer
- Symfony CLI
- Base de données (MySQL)

## Installation

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/FuFu2127/relais-harmonie-backend.git
   cd relais-harmonie-backend

2. Installer les dépendances avec Composer :
   ```bash
   composer install

3. Configurer les variables d’environnement
Copier le fichier .env ou .env.local et modifier les paramètres (base de données) :

    ```bash
   cp .env .env.local
    # puis éditer .env.local

4. Créer la base de données et appliquer les migrations :
   
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate

5. Lancer le serveur

  ```bash
    symfony serve



