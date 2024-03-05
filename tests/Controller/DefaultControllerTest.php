<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexWithoutLogin()
    {
        $client = static::createClient();

        $client->followRedirects();
        $crawler = $client->request('GET', '/');

        $this->assertSame(1, $crawler->filter('input[name="email"]')->count());
        $this->assertSame(1, $crawler->filter('input[name="password"]')->count());
        $this->assertStringContainsString('/login',$crawler->getUri());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
