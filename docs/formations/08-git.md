# 8. Git

## Ce que c'est

**Git** est un outil de gestion de versions : il enregistre l'historique d'un projet, sous forme d'une suite d'instantanés appelés **commits**. **GitHub** est un service qui héberge des dépôts Git et y ajoute des outils de collaboration, comme les pull requests et la CI.

| Terme | Sens |
|---|---|
| Dépôt | Le projet et tout son historique, dans le dossier caché `.git/` |
| Commit | Un instantané du projet, avec un message et un auteur |
| Branche | Une ligne de travail indépendante |
| `main` | La branche principale, celle qui doit rester stable |
| Index | La zone où l'on prépare le prochain commit |
| Dépôt distant | Une copie du dépôt sur un serveur, par exemple sur GitHub |
| Pull request | Une demande de fusion d'une branche, relue et vérifiée avant d'entrer dans `main` |

## Pourquoi ça existe

Git permet de revenir en arrière, de comprendre pourquoi un changement a été fait et de travailler sur plusieurs sujets sans se gêner. Une branche isole un travail en cours : `main` reste fonctionnelle pendant ce temps.

## Où le voir dans ce projet

- [.gitignore](../../.gitignore) : ce que Git ne doit pas suivre.
- [src/Controller/.gitignore](../../src/Controller/.gitignore) : un fichier vide, qui garde un dossier dans Git.
- [.github/workflows/ci.yml](../../.github/workflows/ci.yml) : ce qui se déclenche à chaque `push` sur `main` et à chaque pull request.
- [.env](../../.env) : un fichier versionné, à comparer aux fichiers `.local`, qui ne le sont pas.
- L'historique lui-même, avec `git log`.

## Comment lire ce code

### Le cycle de travail

```text
fichiers modifiés ──git add──> index ──git commit──> historique local ──git push──> GitHub
```

`git add` choisit ce qui entre dans le commit. C'est utile : on peut faire un commit par sujet, même si plusieurs fichiers ont changé.

Le parcours de travail de ce projet :

1. Créer une branche pour le travail depuis un `main` à jour, ici `feat/portfolio`.
2. Faire de petits commits, chacun avec les tests qui le prouvent.
3. Pousser la branche et ouvrir une pull request.
4. Fusionner dans `main` quand la [CI](../ci-cd.md) est verte.
5. Supprimer la branche, dont le travail est maintenant dans `main`.

### Créer une branche

Une branche est une simple étiquette posée sur un commit, qui avance à chaque nouveau commit. La créer ne copie aucun fichier : c'est instantané et sans risque.

On part toujours d'un `main` à jour. Sinon, la branche démarre d'un ancien état et le risque de conflit à la fusion augmente :

```bash
git switch main
git pull
git switch -c feat/portfolio
```

`git switch -c` crée la branche et s'y place. Une branche sert à un seul sujet, et vit le temps de ce sujet : quelques heures ou quelques jours, pas plusieurs mois. Plus elle dure, plus elle s'éloigne de `main`, et plus la fusion devient difficile.

### Nommer une branche

Le nom suit la forme `type/sujet` :

```text
feat/portfolio
│    └── le sujet, court, en minuscules, mots séparés par des tirets
└── le type, le même que dans les commits
```

Les **types** sont ceux des commits, seule la forme change :

| Type | À utiliser pour | Nom de branche | Message de commit |
|---|---|---|---|
| `feat` | Une fonctionnalité | `feat/portfolio` | `feat: ajoute la page d'accueil du portfolio` |
| `fix` | Une correction | `fix/lien-casse-accueil` | `fix: corrige le lien de la page d'accueil` |
| `docs` | La documentation | `docs/conventions-branches` | `docs: explique la création et le nommage des branches` |
| `ci` | L'intégration continue | `ci/cache-composer` | `ci: met en cache les dépendances Composer` |
| `chore` | De la maintenance | `chore/maj-symfony` | `chore: met à jour Symfony` |

`feat/portfolio` et `docs/conventions-branches` existent dans ce dépôt, les autres noms de branche sont des exemples. Le nom dit **sur quoi** on travaille, et le commit dit **ce qui change** : une branche contient souvent plusieurs commits.

Contrairement à Conventional Commits, cette forme n'est pas une norme officielle : c'est une convention répandue, que chaque équipe fixe à sa façon. En entreprise, le nom porte souvent aussi le numéro du ticket.

Git, lui, impose quelques règles : pas d'espace, ni de `~`, `^`, `:` ou `..` dans un nom. Le `/` est permis, mais il range les noms comme des dossiers : une branche `feat` empêche donc de créer `feat/portfolio`. Les accents passent, mais on les évite parce qu'ils sont moins pratiques à taper dans un terminal.

### Lire `.gitignore`

Git ne suit pas ce qui est listé dans [.gitignore](../../.gitignore) :

| Motif | Pourquoi l'ignorer |
|---|---|
| `/vendor/` | Du code recréé par `composer install` |
| `/var/` | Le cache et les journaux, générés à l'exécution |
| `/public/bundles/` | Une copie créée par `assets:install` |
| `/.phpunit.cache/` | Le cache de PHPUnit |
| `/phpunit.xml` | Une configuration PHPUnit personnelle |
| `.DS_Store` | Un fichier technique de macOS |
| `/.env.local`, `/.env.local.php`, `/.env.*.local` | Des réglages et secrets propres à une machine |
| `/config/secrets/**/*.decrypt.private.php` | Les clés privées du coffre-fort de Symfony |

Un `/` au début veut dire « à la racine du projet », et `**` veut dire « dans n'importe quel sous-dossier ». Les commentaires `###> ... ###` délimitent ce que les recettes Flex ont ajouté.

### Ce qui ne doit jamais entrer dans Git

Un fichier commité reste dans l'historique, même supprimé plus tard. Sur un dépôt public, un secret poussé doit être considéré comme connu de tous et changé.

Dans ce projet, `.env.local`, `.env.local.php` et `config/secrets/**/*.decrypt.private.php` ne sont jamais commités, ni copiés ailleurs. Avant de valider, regardez toujours ce qui part avec `git status` et `git diff --staged`. La fiche [configuration](06-configuration-et-environnements.md) explique où vont les secrets.

### Les messages de commit

Les messages suivent **Conventional Commits** : un type, deux-points, puis un résumé. Voici de vrais exemples de l'historique :

| Type | Sens | Exemple |
|---|---|---|
| `feat` | Une fonctionnalité | `feat: ajoute la liste et le détail des projets` |
| `fix` | Une correction | `fix: épingle les actions par SHA et limite le port au local` |
| `chore` | De la maintenance | `chore: ajoute les dépendances de base et la licence` |
| `ci` | L'intégration continue | `ci: ajoute les vérifications de qualité et de Docker` |
| `docs` | La documentation | `docs: ajoute la documentation de formation du niveau` |

Un bon message dit ce que le commit fait une fois appliqué. Ce format rend l'historique lisible d'un coup d'œil.

## Commandes officielles utilisées

```bash
git status
git diff
git diff --staged
```

Voir l'état du dépôt, les changements non préparés et ceux qui sont préparés.

```bash
git switch -c feat/portfolio
git add src/Controller/HomeController.php
git commit -m "feat: ajoute la page d'accueil du portfolio"
```

Créer une branche et s'y placer, préparer un fichier, enregistrer un commit.

```bash
git log --oneline
git show 5112522
git branch
```

Lire l'historique, voir le détail d'un commit, lister les branches. `5112522` est le début de l'identifiant d'un commit de ce dépôt : chaque commit a un **SHA**, une empreinte unique de son contenu.

```bash
git push -u origin feat/portfolio
```

Envoie la branche vers le dépôt distant `origin`. `-u` retient ce lien pour les prochains `git push`.

```bash
git switch main
git pull
git branch -d docs/conventions-branches
git push origin --delete docs/conventions-branches
```

Après la fusion : revenir sur `main`, récupérer la fusion, puis supprimer la branche en local et sur GitHub. `git branch -d` refuse de supprimer une branche qui n'est pas fusionnée, ce qui évite de perdre du travail. Si la pull request a déjà supprimé la branche sur GitHub, la dernière commande est inutile.

## À retenir

- Un commit par idée, avec un message clair.
- Une branche par sujet, créée depuis un `main` à jour et nommée `type/sujet`.
- Les branches et les commits partagent les mêmes types, mais pas la même forme.
- On travaille sur une branche et on fusionne quand la CI est verte.
- `.gitignore` empêche de suivre ce qui est généré ou privé.
- Un secret commité reste dans l'historique : on vérifie avant de valider.
