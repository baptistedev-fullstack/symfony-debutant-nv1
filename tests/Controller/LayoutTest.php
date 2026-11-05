<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LayoutTest extends WebTestCase
{
    public function testNavigationIsOnEveryPage(): void
    {
        $client = static::createClient();

        foreach (['/', '/projets', '/projets/mini-blog'] as $url) {
            $client->request('GET', $url);

            self::assertSelectorExists('nav a[href="/"]', $url);
        }
    }

    public function testCurrentPageIsMarked(): void
    {
        $client = static::createClient();
        $client->request('GET', '/projets');

        self::assertSelectorExists('nav a[aria-current="page"][href="/projets"]');
    }

    public function testPageTitleIsSetByChildTemplate(): void
    {
        $client = static::createClient();
        $client->request('GET', '/projets');

        self::assertPageTitleContains('Projets');
    }

    public function testStylesheetIsLinked(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertSelectorExists('link[rel="stylesheet"][href="/css/app.css"]');
    }
}
