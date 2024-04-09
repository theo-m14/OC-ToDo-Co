<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $token = $crawler->filter('#csrf')->attr('value');

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'email' => 'theo@test.fr',
            'password' => 'pQqMw96K9@ewLAV',
            "_csrf_token" => $token,
        ]);


        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->assertStringContainsString("Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !", $crawler->filter('h1')->text());

    }

    public function testInvalidLogin(){
        $client = static::createClient();

        
        $crawler = $client->request('GET', '/login');

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'email' => 'invalid',
            'password' => 'invalid'
        ]);

        $client->submit($form); 

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertStringContainsString("Invalid credentials.", $crawler->filter('.alert.alert-danger')->text());
    }

    public function testCreateUser(){
        $client = static::createClient();
        
        $crawler = $client->request('GET','/users/create');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $token = $crawler->filter('#csrf')->attr('value');

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'email' => 'theo@test.fr',
            'password' => 'pQqMw96K9@ewLAV',
            "_csrf_token" => $token,
        ]);


        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
