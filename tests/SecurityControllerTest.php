<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
public function testLogin()
{
$client = static::createClient();
$crawler = $client->request('GET', '/login');

// Vérifie que la page de connexion s'affiche correctement
$this->assertResponseIsSuccessful();
$this->assertSelectorTextContains('label[for="inputEmail"]', 'Email address');

// Simule le formulaire de connexion
$form = $crawler->selectButton('Sign in')->form();
$form['_username'] = 'testuser@example.com';
$form['_password'] = 'testpassword';

// Soumet le formulaire
$client->submit($form);

// Suivre la redirection (si nécessaire)
$client->followRedirect('app_home');

// Vérifie que l'utilisateur est redirigé vers la page d'accueil après connexion
$this->assertSelectorTextContains('h1', 'Welcome testuser');
}
}