ToDoList
========

Base du projet #8 : Améliorez un projet existant

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1

## Description

Projet sous Symfony de gestion de tâche de style TODO

## Installation

Pour commencer avec le projet suivez ces étapes:

### Prérequis 

- Il vous faudra Docker et Docker Compose pour installer le projet

### Etapes d'installation

1. **Cloner le repository:**

    ```bash
    git clone https://github.com/theo-m14/OC-ToDo-Co.git
    ```

2. **Naviguer dans le dossier**

   ```bash
    cd OC-ToDo-Co
    ```

3. Monter l'image Symfony
   
    ```bash
    docker build -f Docker/Dockerfile . -t symfony6  
    ```

4. Monter l'ensemble de l'environnement ( mysql / phpmyadmin / symfony )

   ```bash
    cd Docker && docker-compose up  
    ```

5. Pour la première installation il vous faut créer la base de donnée

   Naviguer dans le terminal de l'image Symfony, placer vous dans le dossier d'installation du serveur

    ```bash
    php bin/console doctrine:database:create && php bin/console doctrine:schema:create
    ```
6. Si vous souhaitez exécuter les tests il vous faudra aussi créer les bases de données de test en spécifiant pour chaque commande chainée
       "--env=test"

   ```bash
    php bin/console doctrine:database:create --env=test && php bin/console doctrine:schema:create --env=test
    ```

8. Exécuter les fixtures afin de manipuler le site ( répéter la commande en spécifiant " --env=test " pour les tests 

   ```bash
    php bin/console doctrine:fixtures:load
    ```
   
9. L'application est disponible à l'adresse : 127.0.0.1:8090

10. PhpMyAdmin est sur le port 8081

11. Pour exécuter les tests:

    ```bash
    vendor/bin/phpunit
    ```
    
