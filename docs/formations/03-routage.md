# 3. Le routage

## Ce que c'est

Une **route** associe une URL à du code. Le **routeur** est le composant de Symfony qui, pour chaque requête, cherche la route qui correspond à son chemin et à sa méthode.

Dans ce projet, la méthode `ProjectController::index()` répond à `GET /projets`, parce qu'elle porte cette ligne :

```php
#[Route('/projets', name: 'app_project_index', methods: ['GET'])]
```

## Pourquoi ça existe

Sans routeur, une URL correspondrait à un fichier : `/projets.php`. Avec un routeur, les URL sont indépendantes de l'organisation du code : elles restent lisibles, et on peut déplacer le code sans les changer.

Le routeur permet aussi le chemin inverse : à partir du nom d'une route, on **génère** son URL. Une URL modifiée à un seul endroit se met à jour partout.

## Où le voir dans ce projet

- [src/Controller/HomeController.php](../../src/Controller/HomeController.php) : la route la plus simple, `/`.
- [src/Controller/ProjectController.php](../../src/Controller/ProjectController.php) : une route fixe et une route avec paramètre.
- [config/routes.yaml](../../config/routes.yaml) : ce qui fait lire les attributs `#[Route]`.
- [config/routes/framework.yaml](../../config/routes/framework.yaml) : une route ajoutée seulement en `dev`.
- [config/packages/routing.yaml](../../config/packages/routing.yaml) : les réglages du routeur.
- [templates/partials/_nav.html.twig](../../templates/partials/_nav.html.twig) : des URL générées à partir des noms de routes.
- [tests/Controller/ProjectControllerTest.php](../../tests/Controller/ProjectControllerTest.php) : les cas 404 et 405.

## Comment lire ce code

### L'attribut `#[Route]`

Un **attribut** PHP, écrit `#[...]`, est une information attachée à une classe ou à une méthode. Il ne s'exécute pas tout seul : c'est Symfony qui le lit. La ligne `use Symfony\Component\Routing\Attribute\Route;`, en haut du fichier, indique de quelle classe il s'agit.

Le lien avec le code vient de [config/routes.yaml](../../config/routes.yaml) :

```yaml
controllers:
    resource: routing.controllers
```

Cette entrée demande de charger les routes déclarées par attributs dans les contrôleurs du projet.

### Les trois routes du site

| Nom | Méthode | Chemin | Code appelé |
|---|---|---|---|
| `app_home` | GET | `/` | `HomeController::index()` |
| `app_project_index` | GET | `/projets` | `ProjectController::index()` |
| `app_project_show` | GET | `/projets/{slug}` | `ProjectController::show()` |

Chaque élément de l'attribut a un rôle :

- le premier argument est le **chemin** ;
- `name` donne un nom stable à la route, utilisé pour générer les URL ;
- `methods` limite les méthodes HTTP acceptées.

### Le paramètre de route

Lisez la route de détail dans [src/Controller/ProjectController.php](../../src/Controller/ProjectController.php) :

```php
#[Route('/projets/{slug}', name: 'app_project_show', requirements: ['slug' => '[a-z0-9-]+'], methods: ['GET'])]
public function show(string $slug): Response
```

`{slug}` est une partie variable du chemin. Un **slug** est un identifiant lisible dans une URL, comme `mini-blog`. Pour `/projets/mini-blog`, Symfony appelle `show('mini-blog')` : le nom `{slug}` correspond au nom de l'argument `$slug`.

`requirements` restreint ce qui est accepté, à l'aide d'une **expression régulière**, un langage de description de texte :

- `[a-z0-9-]` veut dire « une lettre minuscule, un chiffre ou un tiret » ;
- `+` veut dire « une fois ou plus ».

### Deux façons d'obtenir une 404

C'est un point important, testé dans [tests/Controller/ProjectControllerTest.php](../../tests/Controller/ProjectControllerTest.php) :

| URL | Passe le `requirements` ? | Qui répond 404 ? |
|---|---|---|
| `/projets/inconnu` | Oui | Le contrôleur est appelé, ne trouve pas le projet et lève l'erreur |
| `/projets/Mauvais_Slug!` | Non : majuscules et ponctuation | Le routeur : le contrôleur n'est jamais appelé |

`requirements` filtre donc la forme du paramètre, avant toute exécution du contrôleur. Il ne dit pas si le projet existe : c'est le rôle du contrôleur.

### Méthode non autorisée

Si le chemin existe mais pas pour cette méthode, le routeur répond 405, et non 404. `testPostIsNotAllowed()` le vérifie avec un `POST` sur `/projets`.

### Générer une URL

Dans [templates/partials/_nav.html.twig](../../templates/partials/_nav.html.twig), le lien n'est pas écrit en dur :

```twig
<a href="{{ path('app_project_index') }}">
```

`path()` reçoit le nom de la route et renvoie `/projets`. Avec un paramètre, on écrit `path('app_project_show', {slug: slug})`, comme dans [templates/project/index.html.twig](../../templates/project/index.html.twig).

### Les autres fichiers de configuration

- [config/routes/framework.yaml](../../config/routes/framework.yaml) ajoute en `dev` la route `_preview_error`, sous le préfixe `/_error`. Elle permet d'afficher les pages d'erreur, par exemple sur `/_error/404`.
- [config/packages/routing.yaml](../../config/packages/routing.yaml) définit `default_uri` : hors d'une requête HTTP, par exemple dans une commande console, le routeur ne connaît pas le domaine, et cette valeur le lui donne. La variable `DEFAULT_URI` vient de [.env](../../.env).

## Commandes officielles utilisées

```bash
php bin/console debug:router
```

Liste toutes les routes. C'est le premier réflexe quand une URL ne répond pas. Sur ce projet :

```text
  Name                Method   Path
  _preview_error      ANY      /_error/{code}.{_format}
  app_home            GET      /
  app_project_index   GET      /projets
  app_project_show    GET      /projets/{slug}
```

```bash
php bin/console router:match /projets/mini-blog
```

Indique quelle route correspond à ce chemin, et pourquoi les autres ne correspondent pas.

`symfony console` peut remplacer `php bin/console` si le Symfony CLI est installé. Dans Docker, on écrit `docker compose exec app php bin/console debug:router`.

## À retenir

- Une route relie un chemin et une méthode à une méthode de contrôleur.
- On génère les URL par nom de route avec `path()`, on ne les écrit pas en dur.
- `requirements` contrôle la forme d'un paramètre, avant le contrôleur.
- Chemin inconnu : 404. Chemin connu avec la mauvaise méthode : 405.
- En cas de doute : `debug:router`.
