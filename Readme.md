# Sneakpeek Readme

## Description
Sneakpeek est un outil puissant qui vous permet de prévisualiser vos modifications avant de les valider. Il vous aide à détecter les erreurs ou les problèmes avant qu'ils ne soient fusionnés dans la base de code principale.

## Fonctionnalités
- **Aperçu en direct** : Voyez vos modifications en temps réel au fur et à mesure que vous les effectuez.
- **Comparaison côte à côte** : Comparez vos modifications avec le code original pour repérer facilement les différences.
- **Coloration syntaxique** : Profitez des avantages de la coloration syntaxique pour divers langages de programmation.
- **Intégration facile** : Sneakpeek s'intègre parfaitement avec les systèmes de contrôle de version populaires comme Git.
- **DaisyUI** : Utilisation de DaisyUI pour des composants UI élégants et réactifs.
- **Symfony** : Framework PHP robuste pour le développement d'applications web.
- **Maildev** : Outil de développement pour tester les emails envoyés par votre application.
- **Stripe** : Intégration de Stripe pour les paiements en ligne.
- **PHPMyAdmin** : Interface web pour gérer la base de données MySQL

## Installation
Pour utiliser Sneakpeek, suivez ces étapes :

1. Clonez le dépôt Sneakpeek sur votre machine locale :
    ```bash
    git clone https://github.com/sneakpeek/sneakpeek.git

    ```

2. Naviguez dans le répertoire Sneakpeek :
    ``` bash
    cd sneakpeek
    ```

3. Installez les dépendances nécessaires :
    ```bash
    npm install
    ```

4. Installez les dépendances PHP avec Composer :
    ```bash
    composer install
    ```

5. Configurez votre base de données dans le fichier `.env` :
    ```properties
    DATABASE_URL="mysql://root:password@127.0.0.1:3306/sneakpeek"
    ```

6. Exécutez les migrations de la base de données :
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

7. Configurez Maildev pour tester les emails :
    ```bash
    maildev
    ```

8. Mettez à jour le fichier `.env` pour utiliser Maildev :
    ```properties
    MAILER_DSN=smtp://localhost:2025
    ```

9. Démarrez le serveur Sneakpeek :
    ```bash
    symfony serve 
    ```

## Utilisation
Une fois le serveur Sneakpeek en cours d'exécution, vous pouvez accéder à l'aperçu en ouvrant votre navigateur web et en naviguant vers `http://localhost:3000`. De là, vous pouvez commencer à apporter des modifications à votre code et voir l'aperçu en direct en action.

## Contribuer
Nous accueillons les contributions de la communauté ! Si vous souhaitez contribuer à Sneakpeek, veuillez suivre nos [directives de contribution](CONTRIBUTING.md).

## Licence
Sneakpeek est sous licence [MIT License](LICENSE).