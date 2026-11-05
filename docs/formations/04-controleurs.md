# 4. Les contrôleurs

## Ce que c'est

Un **contrôleur** est le code qui reçoit la demande après le routage et produit la réponse. En Symfony, c'est une méthode d'une classe, et elle renvoie un objet `Response`.

Le contrôleur fait le lien entre la requête et le reste de l'application. Ici, il choisit les données et le template à afficher.

## Pourquoi ça existe

Séparer les responsabilités rend le code lisible : la route dit **quand** le code s'exécute, le contrôleur **quoi faire**, le template **à quoi ça ressemble**. Un contrôleur doit rester court. Dès le niveau suivant, le travail plus complexe sera déplacé dans des services.

## Où le voir dans ce projet

- [src/Controller/HomeController.php](../../src/Controller/HomeController.php) : le contrôleur minimal.
- [src/Controller/ProjectController.php](../../src/Controller/ProjectController.php) : des données et une gestion du cas « introuvable ».
- [config/services.yaml](../../config/services.yaml) : ce qui rend ces classes utilisables par Symfony.
- [tests/Controller/HomeControllerTest.php](../../tests/Controller/HomeControllerTest.php) et [tests/Controller/ProjectControllerTest.php](../../tests/Controller/ProjectControllerTest.php) : leur comportement attendu.

## Comment lire ce code

### Le contrôleur minimal, ligne par ligne

Ouvrez [src/Controller/HomeController.php](../../src/Controller/HomeController.php).

| Code | Sens |
|---|---|
| `namespace App\Controller;` | Le nom complet de la classe, qui suit le dossier : voir [PSR-4](02-composer-et-flex.md) |
| `use ...;` | Raccourcis vers les classes utilisées dans ce fichier |
| `final class` | Personne ne peut hériter de cette classe |
| `extends AbstractController` | La classe hérite des méthodes utilitaires de Symfony, comme `render()` |
| `#[Route(...)]` | Voir [le routage](03-routage.md) |
| `public function index()` | Une méthode publique : Symfony doit pouvoir l'appeler |
| `: Response` | Le **type de retour** : cette méthode doit renvoyer une `Response` |
| `$this->render('home/index.html.twig')` | Produit le HTML du template et l'enveloppe dans une `Response` |

Le chemin du template est relatif au dossier [templates/](../../templates/).

`$this` désigne l'objet courant. La méthode `render()` n'est pas écrite dans la classe : elle vient d'`AbstractController` par **héritage**.

### Des données sans base de données

Ouvrez [src/Controller/ProjectController.php](../../src/Controller/ProjectController.php). Les projets sont écrits dans une **constante de classe** :

```php
private const PROJECTS = [
    'convertisseur' => [
        'title' => 'Convertisseur',
        'description' => '...',
    ],
    // ...
];
```

- `const` : la valeur ne change jamais.
- `private` : elle n'est visible que dans cette classe.
- `=>` : c'est un **tableau associatif**, dont chaque clé est associée à une valeur. La clé `'convertisseur'` sert de slug.
- `\'` dans une description : une apostrophe protégée, pour qu'elle ne ferme pas la chaîne.

Ce n'est pas ainsi qu'on stockerait de vraies données, mais cela suffit ici : la base de données arrive à `Debutant-Nv3`.

### Passer des données au template

```php
return $this->render('project/index.html.twig', [
    'projects' => self::PROJECTS,
]);
```

`self::` désigne la classe courante, et sert à lire une constante. Le second argument de `render()` est le tableau des variables données au template : côté Twig, la variable s'appelle `projects`.

### Gérer le cas « introuvable »

La ligne centrale de `show()` condense trois idées :

```php
$project = self::PROJECTS[$slug] ?? throw $this->createNotFoundException();
```

1. `self::PROJECTS[$slug]` cherche le projet par sa clé.
2. `??` donne la valeur de gauche si elle existe et n'est pas `null`, sinon évalue la droite.
3. `throw` lève une **exception**, c'est-à-dire une erreur qui interrompt le code en cours.

`createNotFoundException()` crée une exception que Symfony transforme en réponse 404. Le contrôleur n'a pas à construire lui-même cette page d'erreur.

Le `$slug` de cette ligne est l'argument de `show()` : Symfony l'a lu dans l'URL et le passe à la méthode, comme expliqué dans [le routage](03-routage.md).

### Qui appelle ces méthodes ?

Vous ne trouverez aucun `new HomeController()` dans le projet. [config/services.yaml](../../config/services.yaml) déclare chaque classe de `src/` comme **service**, un objet créé et géré par Symfony. `autoconfigure: true` permet à Symfony de reconnaître seul les contrôleurs. Le sujet est approfondi au niveau suivant, consacré aux services et à l'injection de dépendances.

## Commandes officielles utilisées

```bash
symfony console make:controller HomeController
symfony console make:controller ProjectController
```

La commande `make:controller` vient de MakerBundle. Elle a généré le squelette de ces classes et un template. Les méthodes ont ensuite été complétées. Les commandes `make:*` ne servent qu'à créer du code : elles ne le maintiennent pas.

```bash
php bin/console debug:router app_project_show
```

Affiche le détail d'une route, dont le contrôleur qu'elle appelle.

## À retenir

- Un contrôleur reçoit la demande et renvoie une `Response`.
- `render()` produit la `Response` à partir d'un template et de ses variables.
- Pour une ressource absente, on lève `createNotFoundException()`.
- Un contrôleur reste court : il coordonne, il ne porte pas toute la logique.
- Symfony crée les contrôleurs lui-même, on ne fait pas de `new`.
