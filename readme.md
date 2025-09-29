# WordPress Toolkit

## Responsables du projet

Nom Prénom
<email@example.com>
+410000000
Rôle de la personne

## Contacts

_Ajouter ici toutes les personnes en relation avec le projet_

Nom Prénom
<email@example.com>
+410000000
Rôle de la personne

## Accès

_Ajouter ici les URLs d'accès au projet (staging, production)_

## Documentation technique

### Prérequis

- PHP 8.1
- Dernière version de Wordpress
- asdf, asdf-nodejs : https://atoz.hawaii.do/development/asdf/
- Docker (optionnel, mais recommandé) : https://www.docker.com/products/docker-desktop
- Make (optionnel, mais recommandé) : https://www.gnu.org/software/make/

### Installation

Lancer un serveur php contenant wordpress via Local by Flywheel ou MAMP.

Il faut installer le plugin [wordpress-toolkit-plugin](https://github.com/Hawaii-Interactive-CH/wordpress-toolkit-plugin) dans le dossier `./plugins` ou l'upload via l'admin Wordpress et l'activer dans l'administration de Wordpress. Ce plugin permet de charger les fonctionnalités de base du thème.

#### Nouveau projet

1. Télécharger Wordpress sur https://wordpress.org/download/
2. Décompresser et copier les fichiers dans votre dossier web.
3. Vider le dossier des thèmes `./wp-content/themes`
4. Cloner ce dépot git dans votre dossier de thème.
5. Supprimer le dossier `.git` et crer un nouveau dépot git avec `git init`
6. Créer un nouveau projet sur https://git.hawai.li/ et suivre les instructions pour lier a ce dépôt
7. Installer les dépendances `npm install`
8. Copier `.env.example` to `.env` et configurer les variables d'environnement si besoin

#### Projet existant

1. Télécharger Wordpress sur https://wordpress.org/download/
2. Décompresser et copier les fichiers dans votre dossier web.
3. Vider le dossier des thèmes `./wp-content/themes`
4. Cloner le dépot git du projet dans votre dossier de thème.
5. Installer les dépendances `npm install`
6. Copier `.env.example` to `.env` et configurer les variables d'environnement si besoin

### Commandes

- `npm run watch ou dev` : Compile les assets et recharge le browser quand les fichiers changent
- `npm run production ou build` : Compile les assets en mode production (Exectuer cette commande avant de publier un site)

A noter que le projet utilise vitejs pour compiler les assets. Il est possible de modifier le fichier `vite.config.js` pour ajouter des fonctionnalités supplémentaires.

### Developpement

Pour que les assets et `vite` ajoute le `script` dans le head en mode development, il faut ajouter dans le `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

#### Custom Post Type

Pour créer un nouveau CPT, il faut créer un fichier dans le dossier `./toolkit/models/custom` ou via le générateur de CPT intégré au plugin.