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

        $crawler = $client->request('GET','/users/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $token = $crawler->filter('#user__token')->attr('value');

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'test',
            'user[password][first]' => 'pQqMw96K9@ewLAV',
            "user[password][second]" => 'pQqMw96K9@ewLAV',
            "user[email]" => 'test2@test.fr',
            "user[roles]" => 'ROLE_USER',
            "user[_token]" => $token,
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertStringContainsString("L'utilisateur a bien été ajouté.", $crawler->filter('.alert.alert-success')->text());
    }

    public function testEditUser(){
        $client = static::createClient();
        
        $crawler = $client->request('GET','/users');

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

        $crawler = $client->request('GET','/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->filter("tr")->eq(3)->filter('td a')->attr('href');

        $crawler = $client->request('GET',$link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $token = $crawler->filter('#user__token')->attr('value');

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'testEdit',
            'user[password][first]' => 'pQqMw96K9@ewLAV',
            "user[password][second]" => 'pQqMw96K9@ewLAV',
            "user[email]" => 'test2@test.fr',
            "user[roles]" => 'ROLE_ADMIN',
            "user[_token]" => $token,
        ]);

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
