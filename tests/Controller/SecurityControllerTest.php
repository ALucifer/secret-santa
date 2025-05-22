<?php

namespace App\Tests\Controller;

use App\Factory\UserFactory;
use App\Security\Role;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecurityControllerTest extends WebTestCase
{
    use AuthenticateUserTrait;
    use ResetDatabase, Factories;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        UserFactory::createOne([
            'email' => 'user@mail.com',
            'password' => 'test123',
            'roles' => [ Role::USER ],
            'isVerified' => true,
        ]);

    }

    /**
     * @dataProvider formRegisterErrors
     */
    public function testShouldIndicateErrorOnRegisterForm(array $formValues, array $errorsMessage): void
    {
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->filterXPath('//form[@name="register"]')->form();

        $form->setValues($formValues);

        $crawler = $this->client->submit($form);

        $errors = $crawler->filterXPath('//form[@name="register"]//ul//li');

        foreach ($errors as $error) {
            $this->assertContains(
                $error->textContent,
                $errorsMessage
            );
        }
    }

    public function testShouldRedirectToLoginAfterRegister()
    {
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->filterXPath('//form[@name="register"]')->form();

        $form->setValues([
            'register[email]' => 'test@test.com',
            'register[password][first]' => 'MySecretS@nta1',
            'register[password][second]' => 'MySecretS@nta1',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/login');
    }

    /**
     * @dataProvider urlsRedirectedToProfile
     */
    public function testShouldRedirectToProfile(string $url)
    {
        $this->getAuthenticatedClient();
        $this->client->request('GET', $url);

        $this->assertResponseRedirects('/profile');
    }

    public function testShouldIndicateErrorInLoginForm()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->filterXPath('//form[@name="form_login"]')->form();

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $invalidText = $crawler->filterXPath('//form[@name="form_login"]');

        $this->assertMatchesRegularExpression('/Login\/Mot de passe incorrect./', $invalidText->text());

        $inputs = $crawler->filterXPath('//form[@name="form_login"]//input');

        foreach ($inputs as $input) {
            $this->assertStringContainsString(
                'border-red-300 bg-red-50 text-red-900',
                $input->attributes->getNamedItem('class')->nodeValue
            );
        }
    }

    public function testShouldAuthenticateUser()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->filterXPath('//form[@name="form_login"]')->form();

        $form->setValues([
            '_username' => 'user@mail.com',
            '_password' => 'test123',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/profile');
    }

    public function testShouldIncludeRegisterLinkInLoginForm()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->filterXPath('//div[@class="container__information"]//a');

        /** @var Router $router */
        $router = self::getContainer()->get('router');

        $registerLink = $router->generate('register', [], 0);

        $this->assertEquals($registerLink, $link->link()->getUri());
    }

    public function testShouldIncludeForgotPasswordLinkInLoginForm()
    {
        $crawler = $this->client->request('GET', '/login');

        $link = $crawler->filterXPath('//form[@name="form_login"]//a');

        /** @var Router $router */
        $router = self::getContainer()->get('router');

        $forgotLink = $router->generate('app_forgot_password', [], 0);

        $this->assertEquals($forgotLink, $link->link()->getUri());
    }

    /**
     * @group test
     */
    public function testShouldRenderInvalidPasswordWhenEmailIsNotVerified()
    {
        UserFactory::createOne([
            'email' => 'user-invalid@mail.com',
            'password' => 'test123',
            'roles' => [ Role::USER ],
            'isVerified' => false,
            'isInvited' => false,
        ]);

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->filterXPath('//form[@name="form_login"]')->form();

        $form->setValues([
            '_username' => 'user-invalid@mail.com',
            '_password' => 'test123',
        ]);


        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $invalidText = $crawler->filterXPath('//form[@name="form_login"]');

        $this->assertMatchesRegularExpression('/Login\/Mot de passe incorrect./', $invalidText->text());

        $inputs = $crawler->filterXPath('//form[@name="form_login"]//input');

        foreach ($inputs as $input) {
            $this->assertStringContainsString(
                'border-red-300 bg-red-50 text-red-900',
                $input->attributes->getNamedItem('class')->nodeValue
            );
        }
    }

    public function urlsRedirectedToProfile(): array
    {
        return [
            ['/login'],
            ['/register'],
        ];
    }

    public function formRegisterErrors(): array
    {
        return [
            [
                [],
                ['Email obligatoire.', 'Mot de passe obligatoire.']
            ],
            [
                ['register[email]' => 'test@test.com'],
                ['Mot de passe obligatoire.']
            ],
            [
                ['register[password][first]' => 'test'],
                ['Email obligatoire.', 'Votre confirmation de mot de passe doit correspondre à votre mot de passe.']
            ],
            [
                ['register[email]' => 'test@test.com', 'register[password][first]' => 'test'],
                ['Votre confirmation de mot de passe doit correspondre à votre mot de passe.']
            ],
            [
                [
                    'register[email]' => 'test@test.com',
                    'register[password][first]' => 'test',
                    'register[password][second]' => 'test',
                ],
                [
                    'Votre mot de passe doit faire au minimum 8 charactères.',
                    'Votre mot de passe à une sécurité trop faible.'
                ]
            ],
            [
                [
                    'register[email]' => 'test@test.com',
                    'register[password][first]' => 'azertyuiop',
                    'register[password][second]' => 'azertyuiop',
                ],
                [
                    'Votre mot de passe à une sécurité trop faible.'
                ]
            ],
        ];
    }
}