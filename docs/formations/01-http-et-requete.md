# 1. HTTP et la requête

## Ce que c'est

Le web repose sur un échange très simple entre deux programmes :

- le **client**, en général un navigateur, envoie une **requête** ;
- le **serveur** renvoie une **réponse**.

HTTP est le protocole, c'est-à-dire le format convenu de cet échange. Une requête et sa réponse ressemblent à ceci :

```http
GET /projets HTTP/1.1
Host: localhost:8080
```

```http
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8

<!DOCTYPE html>
<html lang="fr">...
```

Le vocabulaire à connaître :

| Terme | Sens | Exemple dans ce projet |
|---|---|---|
| URL | L'adresse complète d'une ressource | `http://localhost:8080/projets` |
| Chemin | La partie de l'URL après le domaine | `/projets` |
| Méthode | Ce que le client veut faire | `GET` : lire une page |
| Code de statut | Le résultat, sous forme de nombre | `200`, `404`, `405` |
| En-têtes | Des informations sur l'échange | `Content-Type` |
| Corps | Le contenu lui-même | Le HTML de la page |

Les codes de statut rencontrés à ce niveau :

| Code | Sens | Quand ce projet le renvoie |
|---|---|---|
| 200 | Tout va bien | Une page existante |
| 404 | Cette ressource n'existe pas | `/projets/inconnu` |
| 405 | Méthode non autorisée | Un `POST` sur `/projets` |

HTTP est **sans état** : chaque requête est indépendante, le serveur ne se souvient pas de la précédente par défaut.

## Pourquoi ça existe

Un format commun permet à n'importe quel navigateur de parler à n'importe quel serveur. Symfony ne fait rien de magique : son travail est de transformer une requête en réponse. Tout le reste du framework sert cette seule transformation.

## Où le voir dans ce projet

- [public/index.php](../../public/index.php) : le **contrôleur frontal**, seul fichier PHP exposé au web.
- [src/Kernel.php](../../src/Kernel.php) : le **Kernel**, le cœur qui reçoit la requête et produit la réponse.
- [src/Controller/ProjectController.php](../../src/Controller/ProjectController.php) : du code qui renvoie un objet `Response`.
- [public/css/app.css](../../public/css/app.css) : un fichier statique, servi directement sans passer par PHP.
- [tests/Controller/ProjectControllerTest.php](../../tests/Controller/ProjectControllerTest.php) : des tests qui vérifient les codes 200, 404 et 405.
- [Dockerfile](../../Dockerfile) : la ligne `FROM` choisit FrankenPHP, le serveur web de ce projet.

## Comment lire ce code

Voici le trajet complet de `GET /projets` :

```text
Navigateur
   │  requête HTTP
   ▼
FrankenPHP (serveur web et PHP)
   │  le fichier demandé n'existe pas dans public/, donc public/index.php
   ▼
public/index.php ──> Kernel
                       │
                       ├─ routeur : /projets correspond à app_project_index
                       ├─ contrôleur : ProjectController::index()
                       └─ Twig : project/index.html.twig
                       │
                       ▼
                     objet Response
   │  réponse HTTP
   ▼
Navigateur
```

Lisez [public/index.php](../../public/index.php). Il ne fait que deux choses :

1. Il charge `vendor/autoload_runtime.php`. Ce fichier active le composant Runtime de Symfony et l'autoload de Composer.
2. Il renvoie une fonction qui crée le `Kernel`. Le Runtime appelle cette fonction, envoie la requête au Kernel et transmet la réponse au client.

La fonction reçoit `$context`, un tableau qui contient les variables d'environnement. C'est de là que viennent `APP_ENV` et `APP_DEBUG`, expliqués dans la [fiche 6](06-configuration-et-environnements.md).

Toutes les URL passent par ce fichier : c'est le principe du **contrôleur frontal**. On n'a donc pas un fichier PHP par page, et un seul endroit gère la sécurité, les erreurs et le routage. Seul `public/` est exposé : `src/`, `config/` et `.env` ne sont jamais accessibles par une URL. C'est pour cela que le serveur a `public/` pour racine.

Ouvrez ensuite [src/Kernel.php](../../src/Kernel.php). Il tient en quelques lignes parce que `MicroKernelTrait` apporte la logique : charger les bundles de [config/bundles.php](../../config/bundles.php), puis la configuration de [config/](../../config/). Un `trait` est un morceau de code PHP réutilisé dans une classe avec `use`. `getAllowedEnvs()` limite `APP_ENV` à `prod`, `dev` et `test`.

Le fichier `app.css` prend un autre chemin : il existe vraiment dans `public/`, donc le serveur le renvoie tel quel, sans démarrer Symfony.

## Commandes officielles utilisées

```bash
curl -i http://localhost:8080/projets
```

`curl` est un client HTTP en ligne de commande. L'option `-i` affiche aussi le statut et les en-têtes de la réponse.

```bash
curl -i -X POST http://localhost:8080/projets
```

`-X POST` change la méthode. Vous verrez le code 405. Avec le site lancé par Docker, l'environnement est `dev` : le corps est une longue page d'erreur de débogage.

Dans le navigateur, l'onglet **Réseau** des outils de développement montre les mêmes informations.

## À retenir

- Une requête entre, une réponse sort : c'est tout le travail de Symfony.
- La méthode et le chemin décrivent la demande, le code de statut décrit le résultat.
- Toutes les pages passent par `public/index.php`, le contrôleur frontal.
- Seul `public/` est exposé au web.
