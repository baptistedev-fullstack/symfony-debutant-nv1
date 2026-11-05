# 6. Configuration et environnements

## Ce que c'est

Le même code tourne dans des situations différentes. Un **environnement**, défini par la variable `APP_ENV`, est un jeu de réglages adapté à une situation :

| `APP_ENV` | Usage | Débogage |
|---|---|---|
| `dev` | Développer sur sa machine | Activé : erreurs détaillées |
| `test` | Lancer les tests automatiques | Activé, sauf demande contraire |
| `prod` | Servir de vrais visiteurs | Désactivé : erreurs génériques |

Une **variable d'environnement** est une valeur donnée au programme de l'extérieur, sans modifier son code.

La liste des environnements permis est écrite dans `getAllowedEnvs()` de [src/Kernel.php](../../src/Kernel.php).

## Pourquoi ça existe

Une page d'erreur détaillée aide le développeur mais révèle trop de choses à un visiteur. Les tests n'ont pas besoin de la même configuration qu'un site en ligne. Et un mot de passe de production ne doit pas se trouver dans le code.

Mettre ces valeurs hors du code permet de garder un seul code et de changer seulement sa configuration.

## Où le voir dans ce projet

- [.env](../../.env) : les valeurs par défaut, versionnées.
- [.env.dev](../../.env.dev) : ce qui est propre à `dev`, versionné.
- [.env.test](../../.env.test) : ce qui est propre à `test`, versionné.
- [config/packages/framework.yaml](../../config/packages/framework.yaml) : l'usage de `APP_SECRET`.
- [config/packages/routing.yaml](../../config/packages/routing.yaml) : l'usage de `DEFAULT_URI`.
- [config/bundles.php](../../config/bundles.php) : les bundles actifs selon l'environnement.
- [config/services.yaml](../../config/services.yaml) : la configuration de vos propres classes.
- [phpunit.dist.xml](../../phpunit.dist.xml) : force `APP_ENV=test` pour les tests.
- [tests/bootstrap.php](../../tests/bootstrap.php) : charge les fichiers `.env` pour les tests.
- [.gitignore](../../.gitignore) : les fichiers qui ne doivent jamais être versionnés.

## Comment lire ce code

### L'ordre de chargement des fichiers `.env`

Le haut de [.env](../../.env) résume la règle. Symfony lit ces fichiers s'ils existent, et chacun l'emporte sur le précédent :

| Ordre | Fichier | Versionné ? | Rôle |
|---|---|---|---|
| 1 | `.env` | Oui | Valeurs par défaut |
| 2 | `.env.local` | Non | Réglages de votre machine |
| 3 | `.env.$APP_ENV` | Oui | Valeurs par défaut d'un environnement |
| 4 | `.env.$APP_ENV.local` | Non | Réglages de votre machine pour cet environnement |

Une vraie variable d'environnement, définie par le système, passe avant tous ces fichiers.

En environnement `test`, `.env.local` n'est pas lu : les tests donnent ainsi le même résultat sur toutes les machines.

Ce projet n'a besoin d'aucun fichier `.local`. S'il vous en faut un, il reste sur votre machine : son contenu n'est jamais versionné, copié ni partagé.

### Les variables de ce projet

| Variable | Où | Rôle |
|---|---|---|
| `APP_ENV` | `.env` | L'environnement choisi, `dev` par défaut |
| `APP_SECRET` | `.env`, `.env.dev`, `.env.test` | La clé qui sert à signer des données |
| `APP_SHARE_DIR` | `.env` | Un dossier partagé, `var/share` |
| `DEFAULT_URI` | `.env` | L'adresse utilisée hors requête HTTP |
| `KERNEL_CLASS` | `.env.test` | La classe Kernel à démarrer pour les tests |

`APP_DEBUG` n'est écrit nulle part : quand il n'est pas défini, Symfony l'active pour tout environnement autre que `prod`. C'est lui qui devient le second argument de `new Kernel()` dans [public/index.php](../../public/index.php).

### Le cas de `APP_SECRET`

Un **secret** est une valeur qui ne doit pas être connue en dehors de l'application. `APP_SECRET` sert à Symfony pour signer des données : sans la clé, personne ne peut produire une signature valide. Il est branché dans [config/packages/framework.yaml](../../config/packages/framework.yaml) :

```yaml
framework:
    secret: '%env(APP_SECRET)%'
```

`%env(...)%` veut dire : « lis cette variable d'environnement ».

Regardez bien où elle a une valeur :

- dans [.env](../../.env), `APP_SECRET=` est **vide** ;
- dans [.env.dev](../../.env.dev) et [.env.test](../../.env.test), elle a une valeur.

Ce n'est pas un oubli. `.env` s'applique à tous les environnements, `prod` compris. Une valeur écrite là, publique sur GitHub, servirait de secret à tout déploiement qui oublie d'en définir un. Vide, elle ne donne aucune fausse sécurité : un vrai environnement doit fournir sa propre valeur.

`.env.dev` et `.env.test` ne sont chargés que pour `dev` et `test`. Leurs valeurs ne servent que sur un poste de développement ou pendant les tests, ne protègent rien de réel et sont publiques : ce ne sont pas de vrais secrets, juste de quoi faire démarrer l'application.

**Un vrai secret ne va jamais dans ces fichiers**, ni dans aucun fichier versionné. Le commentaire en haut de `.env` le dit aussi. Un secret poussé sur Git reste dans l'historique, même si on le supprime ensuite. Les vrais secrets vont dans une variable d'environnement du serveur, dans le coffre-fort de Symfony (`secrets:set`), ou dans `.env.local` sur une machine. La fiche [Git](08-git.md) y revient.

### Les fichiers YAML

**YAML** est un format de configuration fondé sur l'indentation. Les décalages y ont donc un sens.

Le dossier [config/packages/](../../config/packages/) contient un fichier par sujet. Le nom du fichier est libre, ce qui compte est la clé de premier niveau, ici `framework:` ou `twig:`.

La syntaxe `when@` réserve un bloc à un environnement. Dans [config/packages/framework.yaml](../../config/packages/framework.yaml) :

```yaml
when@test:
    framework:
        test: true
```

- `test: true` active le navigateur simulé des tests.
- `session.storage_factory_id` utilise, en test, une session simulée.

`session: true`, juste au-dessus, active les sessions. Le commentaire du fichier précise qu'une session ne démarre que si on la lit ou l'écrit : ce projet ne le fait pas.

Dans [config/packages/routing.yaml](../../config/packages/routing.yaml), `when@prod` ajuste un réglage du routeur pour `prod`, et dans [config/packages/twig.yaml](../../config/packages/twig.yaml), `when@test` rend Twig plus strict.

[config/packages/cache.yaml](../../config/packages/cache.yaml) ne contient que des exemples en commentaires : les réglages par défaut suffisent.

### Le cache

Symfony compile sa configuration en PHP dans `var/cache/`, un dossier par environnement. En `dev`, il recompile tout seul quand un fichier change : une modification de la configuration est prise en compte sans action de votre part. Si le site garde malgré tout un ancien comportement, `cache:clear` repart de zéro.

### Les fichiers qu'on ne modifie pas

- [config/reference.php](../../config/reference.php) est généré : il décrit les options disponibles.
- [config/preload.php](../../config/preload.php) sert à une optimisation de `prod` et n'est pas utilisé à ce niveau.

### Les bundles par environnement

Dans [config/bundles.php](../../config/bundles.php), `['dev' => true]` n'active MakerBundle qu'en `dev`, et `['all' => true]` active un bundle partout.

## Commandes officielles utilisées

```bash
php bin/console about
```

Affiche la version de Symfony, l'environnement, l'état du débogage et les chemins.

```bash
php bin/console debug:config framework
php bin/console config:dump-reference framework
```

La première montre la configuration effective d'un bundle, la seconde toutes les options possibles.

```bash
php bin/console debug:dotenv
```

Montre quels fichiers `.env` sont chargés et la valeur retenue pour chaque variable. Cette sortie affiche les valeurs : ne la copiez pas ailleurs.

```bash
php bin/console cache:clear
php bin/console lint:yaml config
php bin/console lint:container
```

Vider le cache, vérifier la syntaxe des fichiers YAML, vérifier que les services sont correctement configurés. La CI exécute les deux derniers.

```bash
php bin/console secrets:set NOM_DU_SECRET
```

Ajoute un secret au coffre-fort de Symfony. Ce projet n'en a pas besoin : la commande est citée pour savoir où iront les vrais secrets.

## À retenir

- `APP_ENV` choisit l'environnement, et les fichiers `.env` se chargent dans un ordre précis.
- `.env` est versionné : il ne contient que des valeurs par défaut, sans secret.
- `APP_SECRET` est vide dans `.env` parce que ce fichier concerne aussi `prod`.
- Les valeurs de `.env.dev` et `.env.test` sont locales et sans importance : un vrai secret n'y va jamais.
- `when@test` et `when@prod` réservent un réglage à un environnement.
