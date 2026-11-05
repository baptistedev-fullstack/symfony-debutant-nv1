<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{
    public function testIndexListsEveryProject(): void
    {
        $client = static::createClient();
        $client->request('GET', '/projets');

        self::assertResponseIsSuccessful();
        self::assertSelectorCount(3, 'ul.projects li a');
    }

    public function testShowDisplaysProject(): void
    {
        $client = static::createClient();
        $client->request('GET', '/projets/mini-blog');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Mini-blog');
    }

    public function testUnknownSlugReturns404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/projets/inconnu');

        self::assertResponseStatusCodeSame(404);
    }

    public function testPostIsNotAllowed(): void
    {
        $client = static::createClient();
        $client->request('POST', '/projets');

        self::assertResponseStatusCodeSame(405);
    }

    #[DataProvider('malformedSlugs')]
    public function testMalformedSlugReturns404(string $path, string $raw): void
    {
        $client = static::createClient(['debug' => false]);
        $client->request('GET', $path);

        self::assertResponseStatusCodeSame(404);
        self::assertStringNotContainsString($raw, $client->getResponse()->getContent());
    }

    public static function malformedSlugs(): iterable
    {
        yield 'majuscules et ponctuation' => ['/projets/Mauvais_Slug!', 'Mauvais_Slug!'];
        yield 'balise script encodée' => ['/projets/%3Cscript%3E', '<script>'];
    }
}
