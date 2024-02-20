<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexWithoutLogin()
    {
        $client = static::createClient();

        $client->followRedirects();
        $crawler = $client->request('GET', '/');

        $this->assertSame(1, $crawler->filter('input[name="_username"]')->count());
        $this->assertSame(1, $crawler->filter('input[name="_password"]')->count());
        $this->assertContains('/login',$crawler->getUri());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
