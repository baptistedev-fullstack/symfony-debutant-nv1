# 2. Composer et Flex

## Ce que c'est

Un projet Symfony réutilise du code écrit par d'autres. Chaque bibliothèque réutilisée est une **dépendance**, distribuée sous forme de **paquet**.

- **Composer** est le gestionnaire de dépendances de PHP : il télécharge les paquets et les rend utilisables.
- **Packagist** est le catalogue public où Composer les trouve.
- **Symfony Flex** est un plugin de Composer : quand un paquet est installé, il applique sa **recette**, c'est-à-dire le code et la configuration de départ prévus pour ce paquet.
- Un **bundle** est un paquet qui s'intègre à Symfony et ajoute des fonctionnalités à l'application.

## Pourquoi ça existe

Sans Composer, il faudrait copier le code de chaque bibliothèque à la main, trouver les bonnes versions et les mettre à jour une par une. Sans Flex, il faudrait écrire soi-même la configuration de base de chaque paquet.

Symfony est lui-même découpé en composants. On installe seulement ce qui sert : ce projet n'a pas Doctrine, par exemple, puisqu'il n'a pas de base de données.

## Où le voir dans ce projet

- [composer.json](../../composer.json) : ce que le projet demande.
- [composer.lock](../../composer.lock) : ce qui a été réellement installé, version par version.
- [symfony.lock](../../symfony.lock) : les recettes appliquées par Flex.
- [config/bundles.php](../../config/bundles.php) : les bundles activés, mis à jour par Flex.
- [config/packages/twig.yaml](../../config/packages/twig.yaml) : un fichier créé par une recette.
- [.gitignore](../../.gitignore) : les blocs `###> ... ###` viennent aussi des recettes.
- [docs/versions.md](../versions.md) : le rôle de chaque paquet.

Le dossier `vendor/` contient le code téléchargé. Il n'est pas versionné : `composer install` le recrée.

## Comment lire ce code

### `composer.json`

| Clé | Rôle dans ce projet |
|---|---|
| `name`, `description`, `type`, `license` | L'identité du paquet |
| `minimum-stability`, `prefer-stable` | Installer des versions stables |
| `require` | Ce qu'il faut pour que le site tourne |
| `require-dev` | Ce qui ne sert qu'à développer : tests et générateurs de code |
| `config.allow-plugins` | Les plugins Composer autorisés à exécuter du code |
| `config.sort-packages` | Garder les paquets triés dans le fichier |
| `config.bump-after-update` | Remonter les contraintes après une mise à jour |
| `autoload` | Où trouver les classes du projet |
| `autoload-dev` | Où trouver les classes de test |
| `replace` | Déclarer inutiles certains paquets de compatibilité |
| `scripts` | Les commandes lancées après `composer install` ou `update` |
| `conflict` | Interdire le paquet géant `symfony/symfony` |
| `extra.symfony.require` | Limiter tous les paquets Symfony à `8.1.*` |

### Lire une contrainte de version

| Écriture | Exemple | Sens |
|---|---|---|
| `>=` | `"php": ">=8.5.12"` | Cette version ou une plus récente |
| `.*` | `"symfony/console": "8.1.*"` | N'importe quel correctif de la 8.1 |
| `^` | `"phpunit/phpunit": "^13.4"` | Une version compatible : 13.4 ou plus, mais pas 14 |
| `*` | `"ext-ctype": "*"` | N'importe quelle version |

Ces règles s'appuient sur le **versionnage sémantique** : dans `13.4.6`, 13 est la version majeure, qui peut casser la compatibilité ; 4 la mineure, qui ajoute des fonctionnalités ; 6 le correctif.

Le minimum `>=8.5.12` pour PHP n'est pas un hasard : la 8.5.11 a des failles activement exploitées.

Les lignes `ext-ctype` et `ext-iconv` ne sont pas des paquets : ce sont des extensions de PHP qui doivent être présentes.

### `composer.json` contre `composer.lock`

`composer.json` dit « une version 8.1 ». `composer.lock` dit « exactement la v8.1.11 ». Le fichier de verrouillage est versionné pour que tout le monde, y compris Docker et la CI, installe exactement les mêmes versions.

| Commande | Lit | Effet |
|---|---|---|
| `composer install` | `composer.lock` | Installe les versions verrouillées, sans en changer |
| `composer update` | `composer.json` | Cherche de nouvelles versions et réécrit `composer.lock` |
| `composer require` | `composer.json` | Ajoute un paquet, puis met à jour les deux fichiers |

### L'autoload

Regardez cette partie de [composer.json](../../composer.json) :

```json
"autoload": {
    "psr-4": {
        "App\\": "src/"
    }
}
```

Elle se lit : « une classe dont le nom commence par `App\` se trouve dans `src/` ». Ainsi `App\Controller\HomeController` est dans `src/Controller/HomeController.php`. Le **namespace**, l'équivalent d'un dossier pour les noms de classes, suit l'arborescence. C'est la norme **PSR-4**. Grâce à elle, on écrit `use` et PHP charge le fichier tout seul, sans `require`.

### Les recettes et `symfony.lock`

Ouvrez [symfony.lock](../../symfony.lock). À chaque paquet qui a une recette correspond la liste des fichiers créés. On y lit par exemple que `symfony/twig-bundle` a apporté `config/packages/twig.yaml` et `templates/base.html.twig`.

C'est aussi la recette de `symfony/framework-bundle` qui a créé [src/Kernel.php](../../src/Kernel.php) et [public/index.php](../../public/index.php). Une bonne partie de ce dépôt n'a donc pas été écrite à la main.

Le **pack** `symfony/test-pack` montre un autre rôle de Flex. Il a été demandé à l'installation mais n'apparaît pas dans `composer.json` : Flex l'a remplacé par ce qu'il regroupe, `phpunit/phpunit`, `symfony/browser-kit` et `symfony/css-selector`.

### Les scripts

La clé `auto-scripts` lance deux commandes après chaque installation : `cache:clear` vide le cache de Symfony, et `assets:install` copie les fichiers publics des bundles. Ces commandes sont aussi exécutées à la construction de l'image Docker.

## Commandes officielles utilisées

Le projet a été créé ainsi :

```bash
symfony new Debutant-Nv1 --version=8.1
```

Crée un projet Symfony 8.1 minimal avec le Symfony CLI, dépôt Git inclus.

```bash
composer require symfony/twig-bundle symfony/asset
```

Ajoute Twig et la fonction `asset()`. Flex applique la recette de Twig.

```bash
composer require --dev symfony/test-pack symfony/maker-bundle
```

`--dev` place les paquets dans `require-dev`. MakerBundle fournit les commandes `make:*` qui génèrent du code.

Au quotidien :

```bash
composer install
composer validate --strict
composer audit
composer show --direct
composer show --tree
```

Dans l'ordre : installer depuis le fichier de verrouillage, vérifier que `composer.json` est valide et cohérent avec `composer.lock`, chercher les failles connues dans les paquets installés, lister les paquets directs, afficher l'arbre des dépendances.

## À retenir

- `composer.json` exprime un souhait, `composer.lock` fixe la réalité. Les deux sont versionnés.
- `vendor/` n'est jamais versionné.
- En CI et dans Docker, on utilise `composer install`, pas `composer update`.
- Une classe `App\...` se trouve dans `src/...` : c'est PSR-4.
- Avant d'ajouter un fichier de configuration à la main, on regarde si une recette le crée.
