# 5. Twig

## Ce que c'est

**Twig** est un moteur de templates. Un **template** est un fichier HTML avec des trous : Twig les remplit avec les données reçues du contrôleur.

Trois écritures à reconnaître :

| Syntaxe | Rôle | Exemple dans ce projet |
|---|---|---|
| `{{ ... }}` | Afficher une valeur | `{{ project.title }}` |
| `{% ... %}` | Exécuter une instruction | `{% for slug, project in projects %}` |
| `{# ... #}` | Écrire un commentaire | Non utilisé dans ce projet |

## Pourquoi ça existe

Mélanger PHP et HTML rend les deux difficiles à lire. Twig sépare l'affichage de la logique. Il apporte surtout deux protections :

- l'**échappement automatique**, qui empêche une donnée d'être interprétée comme du HTML ;
- l'**héritage de templates**, qui évite de recopier le squelette de la page.

## Où le voir dans ce projet

- [templates/base.html.twig](../../templates/base.html.twig) : le squelette commun.
- [templates/home/index.html.twig](../../templates/home/index.html.twig) : la page d'accueil.
- [templates/project/index.html.twig](../../templates/project/index.html.twig) : une boucle.
- [templates/project/show.html.twig](../../templates/project/show.html.twig) : l'affichage d'une variable.
- [templates/partials/_nav.html.twig](../../templates/partials/_nav.html.twig) : un morceau réutilisé.
- [config/packages/twig.yaml](../../config/packages/twig.yaml) : la configuration de Twig.
- [public/css/app.css](../../public/css/app.css) : le style de ces pages.
- [tests/Controller/LayoutTest.php](../../tests/Controller/LayoutTest.php) : les tests de la mise en page.

## Comment lire ce code

### Le squelette et l'héritage

Ouvrez [templates/base.html.twig](../../templates/base.html.twig). Il contient le HTML commun et deux **blocs**, des zones que les pages peuvent remplacer :

```twig
<title>{% block title %}Portfolio{% endblock %}</title>
...
{% block body %}{% endblock %}
```

Ouvrez ensuite [templates/project/show.html.twig](../../templates/project/show.html.twig) :

```twig
{% extends 'base.html.twig' %}

{% block title %}{{ project.title }}{% endblock %}

{% block body %}
    ...
{% endblock %}
```

`extends` signifie : « pars de ce template, et remplace ces blocs ». Le contenu de `title` dans `base.html.twig` est une valeur par défaut, utilisée si une page ne redéfinit pas le bloc. Tout ce qu'une page enfant écrit doit se trouver dans un bloc.

### L'inclusion

[templates/base.html.twig](../../templates/base.html.twig) contient cette ligne :

```twig
{% include 'partials/_nav.html.twig' %}
```

`include` insère un autre template à cet endroit. La différence avec `extends` : `extends` définit le cadre de la page, `include` y dépose un morceau. Le dossier `partials/` et le tiret bas de `_nav` sont des conventions pour dire que ce fichier n'est pas une page complète.

### La navigation

Dans [templates/partials/_nav.html.twig](../../templates/partials/_nav.html.twig) :

```twig
{% set route = app.current_route %}
<a href="{{ path('app_home') }}"{% if route == 'app_home' %} aria-current="page"{% endif %}>Accueil</a>
```

- `app` est une variable que Symfony met à disposition de tous les templates. `app.current_route` donne le nom de la route en cours.
- `set` crée une variable locale.
- `if` n'ajoute l'attribut que sur le lien de la page courante.
- `aria-current="page"` indique aux lecteurs d'écran que ce lien correspond à la page affichée. C'est une règle d'**accessibilité**. La feuille de style s'appuie sur le même attribut pour afficher ce lien en gras.

### La boucle

Dans [templates/project/index.html.twig](../../templates/project/index.html.twig) :

```twig
{% for slug, project in projects %}
    <li><a href="{{ path('app_project_show', {slug: slug}) }}">{{ project.title }}</a></li>
{% endfor %}
```

`projects` vient de `render()` dans [le contrôleur](04-controleurs.md). C'est un tableau associatif, donc la boucle donne à la fois la clé (`slug`) et la valeur (`project`).

`{slug: slug}` est un tableau Twig : la clé de gauche est le paramètre de route, la valeur de droite est la variable de la boucle.

`project.title` utilise le point : Twig cherche tout seul la clé `title` dans le tableau.

### L'échappement automatique

Quand Twig affiche `{{ project.description }}`, il transforme les caractères spéciaux du HTML, comme `<`, en texte inoffensif. Sans cela, une donnée contenant `<script>` serait exécutée par le navigateur du visiteur : c'est une faille **XSS**, pour _cross-site scripting_.

Ici, les données sont écrites en dur et ne présentent aucun risque, mais cette protection comptera dès qu'elles viendront d'un utilisateur. On n'a rien à faire pour l'activer.

### Les fonctions `path()` et `asset()`

- `path('nom_de_route')` génère une URL : voir [le routage](03-routage.md).
- `asset('css/app.css')` donne l'URL d'un fichier de `public/`. Elle vient du paquet `symfony/asset`. Dans [templates/base.html.twig](../../templates/base.html.twig), elle produit `/css/app.css`, ce que vérifie `testStylesheetIsLinked()`.

### Le HTML du squelette

- `lang="fr"` donne la langue de la page aux navigateurs et aux lecteurs d'écran.
- `charset="UTF-8"` est l'encodage, nécessaire aux accents.
- La balise `viewport` adapte la page à la largeur des téléphones.
- `<main>` et `<nav>` sont des balises **sémantiques** : elles décrivent le rôle de leur contenu.

### Le CSS

[public/css/app.css](../../public/css/app.css) contient trois règles, composées d'un sélecteur et de propriétés. Le sélecteur `nav a[aria-current="page"]` vise les liens de la `nav` qui portent cet attribut. `max-width` et `margin: 0 auto` centrent une colonne de texte.

### La configuration

[config/packages/twig.yaml](../../config/packages/twig.yaml) contient deux réglages :

- `file_name_pattern: '*.twig'` indique quels fichiers Twig doit préparer dans le cache et vérifier avec `lint:twig` ;
- `strict_variables: true`, en environnement `test` seulement, fait échouer le rendu si une variable n'existe pas. Une faute de frappe dans un template casse alors les tests.

## Commandes officielles utilisées

```bash
php bin/console lint:twig templates
```

Vérifie la syntaxe de tous les templates, sans les afficher. La CI l'exécute.

```bash
php bin/console debug:twig
```

Liste les fonctions, filtres et variables disponibles dans les templates.

`base.html.twig` vient de la recette de `symfony/twig-bundle`. Les templates de l'accueil et de la liste des projets viennent de `make:controller`.

## À retenir

- `{{ }}` affiche, `{% %}` exécute.
- `extends` et `block` évitent de recopier le squelette, `include` réutilise un morceau.
- Twig échappe par défaut : c'est une protection contre les failles XSS.
- `path()` pour les liens, `asset()` pour les fichiers de `public/`.
- Pas de logique métier dans un template : il affiche ce qu'il reçoit.
