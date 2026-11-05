# 7. Les tests

## Ce que c'est

Un **test automatisé** est du code qui vérifie un autre code. Il se lance en une commande, autant de fois qu'on veut.

**PHPUnit** est l'outil de test de PHP. Deux grandes familles de tests existent :

| Type | Ce qu'il vérifie | Dans ce projet |
|---|---|---|
| Unitaire | Une classe seule, sans le framework | Aucun : il n'y a pas encore de logique à isoler |
| Fonctionnel | Une fonctionnalité complète, de la requête à la réponse | Tous les tests de `tests/Controller/` |

Le test de `tests/Docs/` est à part : il vérifie la documentation elle-même.

## Pourquoi ça existe

Vérifier chaque page à la main prend du temps et s'oublie. Un test attrape une régression, c'est-à-dire une chose qui marchait et ne marche plus. Il sert aussi de description exacte : pour savoir ce que le site doit faire, lisez ses tests.

Ce projet a été écrit en **TDD**, pour _Test-Driven Development_ : on écrit d'abord un test qui échoue, puis le code qui le fait passer.

## Où le voir dans ce projet

- [tests/Controller/HomeControllerTest.php](../../tests/Controller/HomeControllerTest.php) : la page d'accueil.
- [tests/Controller/ProjectControllerTest.php](../../tests/Controller/ProjectControllerTest.php) : la liste, le détail et les erreurs.
- [tests/Controller/LayoutTest.php](../../tests/Controller/LayoutTest.php) : la navigation, le titre, la feuille de style.
- [tests/Docs/DocsConsistencyTest.php](../../tests/Docs/DocsConsistencyTest.php) : la cohérence de la documentation.
- [tests/bootstrap.php](../../tests/bootstrap.php) : ce qui s'exécute avant les tests.
- [phpunit.dist.xml](../../phpunit.dist.xml) : la configuration de PHPUnit.
- [.env.test](../../.env.test) : les variables de l'environnement de test.

## Comment lire ce code

### Un test, étape par étape

Ouvrez [tests/Controller/HomeControllerTest.php](../../tests/Controller/HomeControllerTest.php) :

```php
public function testHomePageShowsPortfolioTitle(): void
{
    $client = static::createClient();
    $client->request('GET', '/');

    self::assertResponseIsSuccessful();
    self::assertSelectorTextContains('h1', 'Portfolio');
}
```

- La classe étend `WebTestCase`, qui apporte le client et les assertions web.
- Une méthode publique dont le nom commence par `test` est un test. Son nom doit dire ce qu'il vérifie.
- `createClient()` crée un **navigateur simulé** et démarre l'application en `test`. Aucun serveur ne tourne, et aucune requête réseau ne part : la requête est donnée directement au Kernel.
- `request()` exécute la requête.
- Les **assertions** comparent le résultat à l'attendu. Une seule assertion fausse fait échouer le test.

Presque tous les tests suivent cette forme : préparer, agir, vérifier.

### Les assertions utilisées

| Assertion | Ce qu'elle vérifie |
|---|---|
| `assertResponseIsSuccessful()` | Le statut est de la famille 2xx |
| `assertResponseStatusCodeSame(404)` | Le statut est exactement celui-ci |
| `assertSelectorTextContains('h1', 'Portfolio')` | Cet élément contient ce texte |
| `assertSelectorExists('a[href="/projets"]')` | Cet élément existe |
| `assertSelectorCount(3, 'ul.projects li a')` | Il y a tant d'éléments |
| `assertPageTitleContains('Projets')` | Le `<title>` contient ce texte |
| `assertStringNotContainsString($raw, ...)` | Ce texte est absent de la réponse |

Les sélecteurs sont ceux du CSS. C'est le rôle du paquet `symfony/css-selector`. Le client vient de `symfony/browser-kit`.

### Tester plusieurs cas avec un fournisseur de données

Lisez la fin de [tests/Controller/ProjectControllerTest.php](../../tests/Controller/ProjectControllerTest.php). `#[DataProvider('malformedSlugs')]` demande de lancer le même test une fois par jeu de données :

```php
public static function malformedSlugs(): iterable
{
    yield 'majuscules et ponctuation' => ['/projets/Mauvais_Slug!', 'Mauvais_Slug!'];
    yield 'balise script encodée' => ['/projets/%3Cscript%3E', '<script>'];
}
```

`yield` fournit les valeurs une par une. Chaque ligne a un nom, affiché en cas d'échec, et ses valeurs deviennent les arguments `$path` et `$raw` du test. `%3Cscript%3E` est la forme encodée de `<script>` dans une URL.

### Pourquoi `debug => false` dans le test des slugs malformés

Ce test vérifie deux choses : la réponse est une 404, et **la valeur envoyée dans l'URL n'est pas recopiée dans la page**. Une page qui recopie ce que l'utilisateur envoie est le point de départ d'une faille XSS.

```php
$client = static::createClient(['debug' => false]);
```

Par défaut, l'environnement `test` a le débogage activé. Dans ce cas, Symfony renvoie sa page d'erreur de développement, qui affiche le message de l'exception, avec l'URL demandée, et qui contient ses propres balises `<script>`. Le test échouerait donc pour une raison qui n'a rien à voir avec le comportement à vérifier : c'est ce qui se passe si on remet `true`, pour les deux jeux de données.

Avec `debug => false`, le Kernel démarre sans débogage. Symfony renvoie alors la page générique, celle que recevrait un visiteur sur un site en `prod` : elle indique seulement `The server returned a "404 Not Found".` Le test porte ainsi sur ce que voit réellement un visiteur.

`testUnknownSlugReturns404`, qui utilise `/projets/inconnu`, n'a pas besoin de ce réglage : il ne vérifie que le statut.

Pendant les tests, des lignes `[error] Uncaught PHP Exception` s'affichent. C'est le journal de Symfony qui note les 404 provoquées volontairement, ce n'est pas un échec. Seul le résumé final de PHPUnit compte.

### Le test de la documentation

[tests/Docs/DocsConsistencyTest.php](../../tests/Docs/DocsConsistencyTest.php) étend directement `TestCase` : il n'a pas besoin de Symfony, seulement de lire des fichiers.

| Test | Ce qu'il garantit |
|---|---|
| `testRequiredDocsExist` | Les documents attendus existent, et `base-de-donnees.md` n'existe pas |
| `testRelativeLinksResolve` | Chaque lien relatif mène à un fichier du projet |
| `testEveryConceptFileLinksToProjectCode` | Chaque fiche renvoie vers du code |
| `testVersionsMatchLockFile` | `versions.md` donne les versions de `composer.lock`, de `composer.json` et du `Dockerfile` |

Il montre qu'un test ne vise pas que le code : tout ce qui peut se dérégler peut être vérifié.

### La configuration

Dans [phpunit.dist.xml](../../phpunit.dist.xml) :

- `bootstrap="tests/bootstrap.php"` : le fichier exécuté avant tout, qui charge l'autoload et les `.env` ;
- `<server name="APP_ENV" value="test" force="true" />` : force l'environnement de test ;
- `failOnDeprecation`, `failOnNotice`, `failOnWarning` : un simple avertissement fait échouer la suite ;
- `<directory>tests</directory>` : où trouver les tests ;
- `<source>` : le code du projet, dans `src`.

## Commandes officielles utilisées

```bash
php bin/phpunit
```

Lance tous les tests. Le résultat attendu commence par `OK`.

```bash
php bin/phpunit tests/Controller/ProjectControllerTest.php
php bin/phpunit --filter testUnknownSlugReturns404
```

Lance un seul fichier, ou les tests dont le nom correspond.

```bash
docker compose exec app php bin/phpunit
```

Lance les tests dans le conteneur. La CI le fait aussi.

```bash
symfony console make:test
```

Génère le squelette d'une classe de test. `HomeControllerTest` en vient, avec le type `WebTestCase`.

## À retenir

- Un test fonctionnel simule une requête et vérifie la réponse, sans serveur réel.
- Un test : préparer, agir, vérifier.
- Un fournisseur de données évite de recopier un test pour chaque cas.
- `debug => false` fait tester la page d'erreur qu'un visiteur verrait, pas celle du développeur.
- Une fonctionnalité n'est pas finie tant qu'un test ne la prouve pas.
