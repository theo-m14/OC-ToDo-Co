<?php

namespace App\Tests\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class TaskControllerTest extends WebTestCase
{
    public function testFindAll(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks');

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

        $crawler = $client->followRedirect();

        $crawler = $client->request('GET', '/tasks');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Créer une tâche', $crawler->filter('a.btn.btn-info')->text());
    }

    public function testCreate(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks/create');

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

        $crawler = $client->followRedirect();

        $crawler = $client->request('GET', '/tasks/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawlerMode = $crawler->filter('form');

        $token = $crawler->filter('#task__token')->attr('value');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'test de tache',
            'task[content]' => 'ceci est un contenu de tâche',
            'task[_token]' => $token,
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEdit(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks/create');

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

        $crawler = $client->followRedirect();

        $crawler = $client->request('GET', '/tasks');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $link = $crawler->filter('h4:not(.pull-right)')->eq(0)->filter('a')->attr('href');

        $crawler = $client->request('GET', $link);

        $buttonCrawlerMode = $crawler->filter('form');

        $token = $crawler->filter('#task__token')->attr('value');
        
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'test edit',
            'task[content]' => 'test edit de tache',
            'task[_token]' => $token,
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $title = $crawler->filter('div.caption')->eq(0)->filter('h4:not(.pull-right)')->text();
        $description = $crawler->filter('div.caption')->eq(0)->filter('p')->text();
        
        $this->assertEquals('test edit',$title);

        $this->assertEquals('test edit de tache',$description);
    }

    public function testToggleDone(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks/create');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();
        
        $token = $crawler->filter('#csrf')->attr('value');

        $buttonCrawlerMode = $crawler->filter('form')->eq(0);
        $form = $buttonCrawlerMode->form([
            'email' => 'theo@test.fr',
            'password' => 'pQqMw96K9@ewLAV',
            "_csrf_token" => $token,
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $crawler = $client->request('GET', '/tasks');

        $firstTask = $crawler->filter('div.thumbnail')->eq(0);

        $formToggle = $firstTask->filter('form')->eq(0);

        $client->submit($formToggle->form());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $success = $crawler->filter('div.alert-success')->text();

        $this->assertStringContainsString('a bien été marquée comme faite',$success);
    }

    public function testDeleteAnonymousTaskOnAdmin(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $token = $crawler->filter('#csrf')->attr('value');

        $buttonCrawlerMode = $crawler->filter('form')->eq(0);
        $form = $buttonCrawlerMode->form([
            'email' => 'theo@test.fr',
            'password' => 'pQqMw96K9@ewLAV',
            "_csrf_token" => $token,
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $crawler = $client->request('GET', '/tasks');

        $firstTask = $crawler->filter('div.thumbnail')->eq(0);

        $formDelete = $firstTask->filter('form')->eq(1);

        $client->submit($formDelete->form());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $success = $crawler->filter('div.alert-success')->text();

        $this->assertStringContainsString('Superbe ! La tâche a bien été supprimée.',$success);
    }

    public function testDeleteAnonymousTaskOnUserRole(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $token = $crawler->filter('#csrf')->attr('value');

        $buttonCrawlerMode = $crawler->filter('form')->eq(0);
        $form = $buttonCrawlerMode->form([
            'email' => 'testuser2@test.fr',
            'password' => 'pQqMw96K9@ewLAV',
            "_csrf_token" => $token,
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $crawler = $client->request('GET', '/tasks');

        $firstTask = $crawler->filter('div.thumbnail')->eq(0);

        $formDelete = $firstTask->filter('form')->eq(1);

        $client->submit($formDelete->form());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $error = $crawler->filter('div.alert-danger')->text();

        $this->assertStringContainsString('Vous devez être propiétaire de la tâche pour la supprimer',$error);
    }

    function testDeleteNotOwnedTask(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $token = $crawler->filter('#csrf')->attr('value');

        $buttonCrawlerMode = $crawler->filter('form')->eq(0);
        $form = $buttonCrawlerMode->form([
            'email' => 'testuser2@test.fr',
            'password' => 'pQqMw96K9@ewLAV',
            "_csrf_token" => $token,
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $crawler = $client->request('GET', '/tasks');

        $lastTask = $crawler->filter('div.thumbnail')->last();

        $formDelete = $lastTask->filter('form')->eq(1);

        $client->submit($formDelete->form());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $error = $crawler->filter('div.alert-danger')->text();

        $this->assertStringContainsString('Vous devez être propiétaire de la tâche pour la supprimer',$error);
    }

    function testDeleteOwnedTask(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $token = $crawler->filter('#csrf')->attr('value');

        $buttonCrawlerMode = $crawler->filter('form')->eq(0);
        $form = $buttonCrawlerMode->form([
            'email' => 'theo@test.fr',
            'password' => 'pQqMw96K9@ewLAV',
            "_csrf_token" => $token,
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $crawler = $client->request('GET', '/tasks');

        $lastTask = $crawler->filter('div.thumbnail')->last();

        $formDelete = $lastTask->filter('form')->eq(1);

        $client->submit($formDelete->form());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $success = $crawler->filter('div.alert-success')->text();

        $this->assertStringContainsString('Superbe ! La tâche a bien été supprimée.',$success);
    }
    

}