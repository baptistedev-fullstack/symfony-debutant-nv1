# Arborescence du projet

Chaque dossier et fichier du projet, avec son rôle et son origine. La colonne « Origine » indique qui l'a créé : **écrit** veut dire écrit pour ce projet, **recette** veut dire créé par une [recette Flex](formations/02-composer-et-flex.md), **généré** veut dire créé par un outil.

```text
Debutant-Nv1/
├── .github/workflows/    Intégration continue
├── bin/                  Exécutables du projet
├── config/               Configuration de Symfony
├── docs/                 Documentation de formation
├── public/               Seul dossier exposé au web
├── src/                  Code PHP de l'application
├── templates/            Templates Twig
├── tests/                Tests automatisés
├── var/                  Cache et journaux (non versionné)
└── vendor/               Dépendances (non versionné)
```

## Racine

| Fichier | Rôle | Origine |
|---|---|---|
| [README.md](../README.md) | Présentation du niveau et démarrage | Écrit |
| [AGENTS.md](../AGENTS.md) | Consignes pour les assistants de code | Recette |
| [LICENSE](../LICENSE) | Licence MIT | Écrit |
| [composer.json](../composer.json) | Les dépendances demandées | Généré, puis complété |
| [composer.lock](../composer.lock) | Les versions exactes installées | Généré par Composer |
| [symfony.lock](../symfony.lock) | Les recettes appliquées | Généré par Flex |
| [Dockerfile](../Dockerfile) | La recette de l'image Docker, voir [docker.md](docker.md) | Écrit |
| [compose.yaml](../compose.yaml) | Le lancement du conteneur | Écrit |
| [.dockerignore](../.dockerignore) | Ce qui n'entre pas dans l'image | Écrit |
| [.env](../.env) | Les variables par défaut | Recette |
| [.env.dev](../.env.dev) | Les variables de `dev` | Recette |
| [.env.test](../.env.test) | Les variables de `test` | Recette |
| [.gitignore](../.gitignore) | Ce que Git ne suit pas | Recette, puis complété |
| [.editorconfig](../.editorconfig) | Le format des fichiers pour les éditeurs | Recette |
| [phpunit.dist.xml](../phpunit.dist.xml) | La configuration de PHPUnit | Recette |

Les fichiers `.env.local`, `.env.local.php` et `.env.*.local` sont personnels : ils sont ignorés par Git et absents de l'image, mais visibles du conteneur lancé (voir [docker.md](docker.md)), et ne sont pas nécessaires à ce projet. `.DS_Store` est un fichier technique de macOS, lui aussi ignoré.

## `.github/workflows/`

| Fichier | Rôle |
|---|---|
| [ci.yml](../.github/workflows/ci.yml) | La CI GitHub Actions, voir [ci-cd.md](ci-cd.md) |

## `bin/`

| Fichier | Rôle | Origine |
|---|---|---|
| [console](../bin/console) | Point d'entrée des commandes Symfony | Recette |
| [phpunit](../bin/phpunit) | Lanceur des tests | Recette |

## `config/`

Voir [la fiche sur la configuration](formations/06-configuration-et-environnements.md).

| Fichier | Rôle | Origine |
|---|---|---|
| [bundles.php](../config/bundles.php) | Les bundles actifs par environnement | Flex |
| [routes.yaml](../config/routes.yaml) | Le chargement des routes par attributs | Recette |
| [routes/framework.yaml](../config/routes/framework.yaml) | La route d'aperçu des erreurs, en `dev` | Recette |
| [services.yaml](../config/services.yaml) | La déclaration des classes de `src/` comme services | Recette |
| [preload.php](../config/preload.php) | Une optimisation de `prod`, inutilisée ici | Recette |
| [reference.php](../config/reference.php) | La description des options, à ne pas modifier | Généré |
| [packages/cache.yaml](../config/packages/cache.yaml) | Le cache applicatif, sans réglage actif | Recette |
| [packages/framework.yaml](../config/packages/framework.yaml) | Le secret, la session et le mode test | Recette |
| [packages/routing.yaml](../config/packages/routing.yaml) | Les réglages du routeur | Recette |
| [packages/twig.yaml](../config/packages/twig.yaml) | Les réglages de Twig | Recette |

## `docs/`

| Fichier | Rôle |
|---|---|
| [arborescence.md](arborescence.md) | Ce document |
| [versions.md](versions.md) | Les versions de PHP, Symfony, FrankenPHP et des paquets |
| [docker.md](docker.md) | Docker expliqué ligne par ligne |
| [ci-cd.md](ci-cd.md) | La CI expliquée étape par étape |
| [formations/index.md](formations/index.md) | L'ordre de lecture et le parcours du code |
| [formations/01-http-et-requete.md](formations/01-http-et-requete.md) | HTTP et le contrôleur frontal |
| [formations/02-composer-et-flex.md](formations/02-composer-et-flex.md) | Les dépendances et les recettes |
| [formations/03-routage.md](formations/03-routage.md) | Les routes |
| [formations/04-controleurs.md](formations/04-controleurs.md) | Les contrôleurs |
| [formations/05-twig.md](formations/05-twig.md) | Les templates |
| [formations/06-configuration-et-environnements.md](formations/06-configuration-et-environnements.md) | Les environnements et les secrets |
| [formations/07-tests.md](formations/07-tests.md) | Les tests |
| [formations/08-git.md](formations/08-git.md) | Git et l'historique |

Il n'y a pas de `base-de-donnees.md` : ce niveau n'a pas de base.

## `public/`

| Fichier | Rôle | Origine |
|---|---|---|
| [index.php](../public/index.php) | Le contrôleur frontal, voir [la fiche HTTP](formations/01-http-et-requete.md) | Recette |
| [css/app.css](../public/css/app.css) | La feuille de style du site | Écrit |

## `src/`

| Fichier | Rôle | Origine |
|---|---|---|
| [Kernel.php](../src/Kernel.php) | Le cœur de l'application | Recette |
| [Controller/HomeController.php](../src/Controller/HomeController.php) | La page d'accueil | `make:controller`, puis complété |
| [Controller/ProjectController.php](../src/Controller/ProjectController.php) | La liste et le détail des projets | `make:controller`, puis complété |
| [Controller/.gitignore](../src/Controller/.gitignore) | Un fichier vide qui garde le dossier dans Git | Recette |

## `templates/`

| Fichier | Rôle | Origine |
|---|---|---|
| [base.html.twig](../templates/base.html.twig) | Le squelette de toutes les pages | Recette, puis complété |
| [home/index.html.twig](../templates/home/index.html.twig) | La page d'accueil | `make:controller`, puis complété |
| [project/index.html.twig](../templates/project/index.html.twig) | La liste des projets | `make:controller`, puis complété |
| [project/show.html.twig](../templates/project/show.html.twig) | Le détail d'un projet | Écrit |
| [partials/_nav.html.twig](../templates/partials/_nav.html.twig) | La navigation | Écrit |

## `tests/`

| Fichier | Rôle |
|---|---|
| [bootstrap.php](../tests/bootstrap.php) | Le démarrage des tests |
| [Controller/HomeControllerTest.php](../tests/Controller/HomeControllerTest.php) | Les tests de la page d'accueil |
| [Controller/ProjectControllerTest.php](../tests/Controller/ProjectControllerTest.php) | Les tests des pages de projets |
| [Controller/LayoutTest.php](../tests/Controller/LayoutTest.php) | Les tests de la mise en page |
| [Docs/DocsConsistencyTest.php](../tests/Docs/DocsConsistencyTest.php) | Les tests de cohérence de la documentation |

## Dossiers générés

Ces dossiers n'existent pas sur GitHub : ils sont recréés sur chaque machine et ignorés par Git.

| Dossier | Contenu | Recréé par |
|---|---|---|
| `vendor/` | Le code des dépendances | `composer install` |
| `var/cache/` | La configuration compilée, par environnement | Symfony, à la première requête |
| `var/log/` | Les journaux | Symfony |
| `.phpunit.cache/` | Le cache de PHPUnit | PHPUnit |
| `public/bundles/` | Les fichiers publics des bundles, s'il y en a | `assets:install` |
