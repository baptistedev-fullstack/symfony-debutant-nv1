# Guide de formation : Debutant Nv1

Ce guide accompagne la lecture du code. Vous n'avez rien à écrire : ouvrez chaque fiche à côté des fichiers qu'elle cite et suivez les liens.

## Ce que vous saurez expliquer à la fin

- Ce qui se passe entre une URL et la page HTML reçue.
- Comment Symfony associe une URL à une méthode PHP.
- Comment un contrôleur passe des données à un template Twig.
- Pourquoi un projet Symfony a autant de fichiers de configuration, et à quoi ils servent.
- Comment les tests prouvent que le site fonctionne.

## Ordre de lecture

Chaque fiche suit le même plan : ce que c'est, pourquoi ça existe, où le voir dans ce projet, comment lire ce code, les commandes officielles utilisées, à retenir.

| Ordre | Fiche | Question à laquelle elle répond |
|---|---|---|
| 1 | [HTTP et la requête](01-http-et-requete.md) | Que se passe-t-il quand on ouvre une page ? |
| 2 | [Composer et Flex](02-composer-et-flex.md) | D'où viennent tous ces fichiers ? |
| 3 | [Le routage](03-routage.md) | Comment une URL trouve-t-elle son code ? |
| 4 | [Les contrôleurs](04-controleurs.md) | Où s'écrit la réponse ? |
| 5 | [Twig](05-twig.md) | Comment le HTML est-il produit ? |
| 6 | [Configuration et environnements](06-configuration-et-environnements.md) | Pourquoi le site se comporte-t-il différemment selon l'endroit où il tourne ? |
| 7 | [Les tests](07-tests.md) | Comment prouver que tout marche ? |
| 8 | [Git](08-git.md) | Comment l'historique du projet est-il tenu ? |

Puis deux documents de référence, à lire une fois et à rouvrir au besoin :

- [Docker](../docker.md) : comment le site tourne dans un conteneur.
- [CI/CD](../ci-cd.md) : ce que GitHub vérifie à chaque changement.

Deux autres servent de carte : [l'arborescence](../arborescence.md) et [les versions](../versions.md).

## Parcours du code conseillé

Suivez une vraie requête, `GET /projets/mini-blog`, fichier par fichier.

| Étape | Fichier | Ce qu'il faut y regarder |
|---|---|---|
| 1 | [public/index.php](../../public/index.php) | Le point d'entrée unique : il crée le Kernel |
| 2 | [src/Kernel.php](../../src/Kernel.php) | Presque vide : tout vient de `MicroKernelTrait` |
| 3 | [config/routes.yaml](../../config/routes.yaml) | La ligne qui fait lire les attributs `#[Route]` |
| 4 | [src/Controller/ProjectController.php](../../src/Controller/ProjectController.php) | La route `app_project_show`, puis la méthode `show()` |
| 5 | [templates/project/show.html.twig](../../templates/project/show.html.twig) | La page, qui hérite de la mise en page |
| 6 | [templates/base.html.twig](../../templates/base.html.twig) | Le squelette HTML commun |
| 7 | [templates/partials/_nav.html.twig](../../templates/partials/_nav.html.twig) | La navigation incluse sur chaque page |
| 8 | [public/css/app.css](../../public/css/app.css) | La feuille de style, servie sans passer par PHP |
| 9 | [tests/Controller/ProjectControllerTest.php](../../tests/Controller/ProjectControllerTest.php) | La preuve que tout ce parcours fonctionne |

Vous pouvez ensuite refaire le même parcours avec `GET /`, qui passe par [src/Controller/HomeController.php](../../src/Controller/HomeController.php) et [templates/home/index.html.twig](../../templates/home/index.html.twig).

## Pour essayer

Rien de tout cela n'est obligatoire, mais voir le site tourner aide à lire le code.

```bash
docker compose up -d --build
docker compose exec app php bin/console debug:router
docker compose exec app php bin/phpunit
docker compose down -v
```

Le site répond sur <http://localhost:8080>.
