# Symfony Debutant Nv1 : portfolio statique

Premier niveau du parcours de formation Symfony. Le projet est un petit portfolio : une page d'accueil, une liste de projets et une page de détail. Il n'y a ni base de données, ni formulaire, ni connexion.

Le but n'est pas le portfolio lui-même, mais de comprendre ce qui se passe entre une URL tapée dans le navigateur et la page HTML reçue : **routes, contrôleurs et Twig**, avec Docker, les tests et la CI autour.

Ce dépôt se lit : le code d'un côté, la documentation de l'autre. Commencez par [le guide de formation](docs/formations/index.md).

## Prérequis

- Docker avec Docker Compose : c'est tout ce qu'il faut pour lancer le site.
- Pour travailler hors de Docker : PHP 8.5.12 ou plus, Composer, et si possible le [Symfony CLI](https://symfony.com/download). Installez ensuite les dépendances avec `composer install`, puis lancez le serveur avec `symfony serve`.

## Démarrage

```bash
docker compose up -d --build
```

Ouvrez ensuite <http://localhost:8080>. Le site n'est accessible que depuis votre machine. La première réponse peut demander quelques secondes.

Pour lancer les tests puis tout arrêter :

```bash
docker compose exec app php bin/phpunit
docker compose down -v
```

L'image Docker de ce dépôt sert uniquement au développement local : elle ne doit être ni exposée ni publiée. Les raisons sont dans [docs/docker.md](docs/docker.md).

## Pages du site

| URL | Contenu |
|---|---|
| `/` | Accueil |
| `/projets` | Liste des projets |
| `/projets/{slug}` | Détail d'un projet, par exemple `/projets/mini-blog` |

## Documentation

| Document | Contenu |
|---|---|
| [docs/formations/index.md](docs/formations/index.md) | Ordre de lecture et parcours du code |
| [docs/arborescence.md](docs/arborescence.md) | Chaque dossier et fichier du projet |
| [docs/versions.md](docs/versions.md) | PHP, Symfony, FrankenPHP et chaque paquet |
| [docs/docker.md](docs/docker.md) | Chaque ligne de `Dockerfile`, `compose.yaml` et `.dockerignore` |
| [docs/ci-cd.md](docs/ci-cd.md) | Chaque job et chaque étape de la CI |

Ce niveau n'a pas de base de données : il n'y a donc pas de `docs/base-de-donnees.md`.

## Navigation dans le parcours

- Niveau précédent : aucun, c'est le premier niveau.
- Niveau suivant : [symfony-debutant-nv2](https://github.com/baptistedev-fullstack/symfony-debutant-nv2).

## Licence

MIT, voir [LICENSE](LICENSE).
