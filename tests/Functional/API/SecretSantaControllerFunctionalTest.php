<?php

namespace App\Tests\Functional\API;

use App\Entity\Member;
use App\Entity\SecretSanta;
use App\Factory\MemberFactory;
use App\Factory\SecretSantaFactory;
use App\Factory\UserFactory;
use App\Repository\MemberRepository;
use App\Repository\SecretSantaRepository;
use App\Repository\UserRepository;
use App\Tests\Helper\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecretSantaControllerFunctionalTest extends AbstractWebTestCase
{
    use ResetDatabase, Factories;

    private SecretSanta $secretSanta;
    private Member $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->secretSanta = SecretSantaFactory::createOne();
        $this->member = MemberFactory::createOne([
            'secretSanta' => $this->secretSanta
        ]);
    }

    #[Group('done')]
    #[DataProvider('urls')]
    public function testShouldUnauthorizeUser(string $url, string $method): void
    {
        $url = str_replace(
            [ '{id}', '{secretSantaMember}' ],
            [ $this->secretSanta->getId(), $this->member->getId() ],
            $url
        );

        $this->client->request($method, $url);

        $this->assertResponseStatusCodeSame(401);
    }

    #[Group('done')]
    public function testShouldCreateNewMemberWithExistingUser(): void
    {
        $user = UserFactory::createOne([
            'email' => 'user@mail.com'
        ]);

        $this->assertCount(0, $user->getParticipationSecretSantaHasMember());

        $client = $this->getAuthenticatedJsonClient();

        $secretSanta = SecretSantaFactory::createOne([
            'owner' => $this->authenticatedUser
        ]);

        $payload = [
            'email' => $user->getEmail()
        ];

        $client->jsonRequest(
            'POST',
            '/api/secret-santa/' . $secretSanta->getId() . '/register/member',
            $payload,
        );

        $this->assertResponseIsSuccessful();

        $user = $this->getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@mail.com']);

        $this->assertCount(1, $user->getParticipationSecretSantaHasMember());
    }

    #[Group('done')]
    public function testShouldNotCreateNewMemberIfItsNotOwner(): void
    {
        $client = $this->getAuthenticatedJsonClient();

        $secretSanta = SecretSantaFactory::createOne();

        $client->jsonRequest(
            'POST',
            '/api/secret-santa/' . $secretSanta->getId() . '/register/member',
            [
                'email' => 'user@mail.com'
            ]
        );

        $this->assertResponseStatusCodeSame(403);
    }

    #[Group('done')]
    public function testShouldCreateUserAndMemberWhenHeIsNotExisting(): void
    {
        $email = 'user@mail.com';
        $user = $this->getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
        $this->assertNull($user);

        $client = $this->getAuthenticatedJsonClient();

        $secretSanta = SecretSantaFactory::createOne([
            'owner' => $this->authenticatedUser
        ]);

        $client->jsonRequest(
            'POST',
            '/api/secret-santa/' . $secretSanta->getId() . '/register/member',
            [
                'email' => $email,
            ],
        );

        $this->assertResponseIsSuccessful();

        $user = $this->getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);

        $this->assertEquals($email, $user->getEmail());
        $this->assertCount(1, $user->getParticipationSecretSantaHasMember());
    }

    #[Group('done')]
    public function testShouldIndicateErrorBody(): void
    {
        $client = $this->getAuthenticatedJsonClient();

        $secretSanta = SecretSantaFactory::createOne([
            'owner' => $this->authenticatedUser
        ]);

        $client->jsonRequest(
            'POST',
            '/api/secret-santa/' . $secretSanta->getId() . '/register/member',
            [
                'email' => 'error'
            ],
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertjson('{"violations":[{"property":"email","message":"This value is not a valid email address."}]}');
    }

    #[Group('done')]
    public function testShouldIndicateNotFoundWhenMemberIsNotInSecretSanta(): void
    {
        $client = $this->getAuthenticatedJsonClient();

        $secretSanta = SecretSantaFactory::createOne([
            'owner' => $this->authenticatedUser
        ]);

        $client->jsonRequest(
            'DELETE',
            '/api/secret-santa/' . $secretSanta->getId() . '/delete/member/1',
        );

        $this->assertResponseStatusCodeSame(404);
    }

    #[Group('done')]
    public function testShouldDeleteMember(): void
    {
        $client = $this->getAuthenticatedJsonClient();

        $secretSanta = SecretSantaFactory::createOne([
            'owner' => $this->authenticatedUser
        ]);

        $member = MemberFactory::createOne([
            'secretSanta' => $secretSanta
        ]);

        $client->jsonRequest(
            'DELETE',
            '/api/secret-santa/' . $secretSanta->getId() . '/delete/member/' . $member->getId(),
        );

        $this->assertResponseIsSuccessful();

        $member = $this->getContainer()->get(MemberRepository::class)->findOneBy(['id' => $member->getId()]);

        $this->assertNull($member);
    }

    #[Group('done')]
    public function testShouldReturnMembersDtoInsteadOfEntity(): void
    {
        $client = $this->getAuthenticatedJsonClient();

        $secretSanta = SecretSantaFactory::createOne([
            'owner' => $this->authenticatedUser
        ]);

        MemberFactory::createMany(10, [ 'secretSanta' => $secretSanta ]);

        $client->jsonRequest(
            'GET',
            '/api/secret-santa/' . $secretSanta->getId() . '/members',
        );

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($content);
        $this->assertCount(10, $content);

        $this->assertArrayHasKey('id', $content[0]);
        $this->assertArrayHasKey('email', $content[0]);
        $this->assertArrayHasKey('invitationAccepted', $content[0]);
        $this->assertArrayHasKey('secretSantaId', $content[0]);
        $this->assertArrayHasKey('userId', $content[0]);
    }

    #[Group('done')]
    public function testShouldIndicateErrorsInRequestForNewSecret(): void
    {
        $client = $this->getAuthenticatedJsonClient();

        $client->jsonRequest(
            'POST',
            '/api/secret-santa',
            [

            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertjson('{"violations":[{"property":"label","message":"This value should not be of type string."}]}');
    }

    #[Group('done')]
    public function testShouldOnlyCreateSecretSanta(): void
    {
        $secretSanta = $this->getContainer()->get(SecretSantaRepository::class)->findAll();

        $this->assertCount(1, $secretSanta);

        $client = $this->getAuthenticatedJsonClient();

        $client->jsonRequest(
            'POST',
            '/api/secret-santa',
            [
                'label' => 'Mon super secret santa !',
            ],
        );

        $this->assertResponseIsSuccessful();
        $content = json_decode($client->getResponse()->getContent(), true);

        $expectedSecretSanta = $this->getContainer()->get(SecretSantaRepository::class)->findAll();

        $members = $this->getContainer()->get(MemberRepository::class)->findBy(['secretSanta' => $content['id']]);

        $this->assertCount(0, $members);
        $this->assertCount(2, $expectedSecretSanta);
    }

    #[Group('done')]
    public function testShouldCreateSecretSantaAndOwnerAsMember(): void
    {
        $secretSanta = $this->getContainer()->get(SecretSantaRepository::class)->findAll();

        $this->assertCount(1, $secretSanta);

        $client = $this->getAuthenticatedJsonClient();

        $client->jsonRequest(
            'POST',
            '/api/secret-santa',
            [
                'label' => 'Mon super secret santa !',
                'registerMe' => true,
            ],
        );

        $this->assertResponseIsSuccessful();
        $content = json_decode($client->getResponse()->getContent(), true);

        $expectedSecretSanta = $this->getContainer()->get(SecretSantaRepository::class)->findAll();

        $members = $this->getContainer()->get(MemberRepository::class)->findBy(['secretSanta' => $content['id']]);

        $this->assertCount(1, $members);
        $this->assertCount(2, $expectedSecretSanta);
    }

    public static function urls(): array
    {
        return [
            ['/api/secret-santa/{id}/register/member', 'POST'],
            ['/api/secret-santa/{id}/delete/member/{secretSantaMember}', 'DELETE'],
            ['/api/secret-santa/1/members', 'GET'],
            ['/api/secret-santa', 'POST'],
        ];
    }
}