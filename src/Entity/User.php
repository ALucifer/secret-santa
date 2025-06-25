<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Security\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;


#[
    ORM\Entity(repositoryClass: UserRepository::class),
    ORM\Table(name: '`user`'),
    ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email']),
]
#[
    UniqueEntity('email', 'Cet email est déjà utilisé.')
]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType

    #[ORM\Column(length: 180)]
    #[NotBlank(message: 'Email obligatoire.')]
    #[Email]
    private string $email;

    /**
     * @var array<string> The user roles
     */
    #[ORM\Column()]
    private array $roles = [];

    #[ORM\Column(options: ['default' => false])]
    private bool $isVerified = false;

    #[ORM\Column()]
    #[NotBlank(message: 'Mot de passe obligatoire.')]
    #[Length(min: 8, minMessage: 'Votre mot de passe doit faire au minimum 8 charactères.')]
    #[PasswordStrength(message: 'Votre mot de passe à une sécurité trop faible.')]
    private string $password;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $isInvited = false;

    /**
     * @var Collection<int, SecretSanta> $secretSantas
     */
    #[ORM\OneToMany(targetEntity: SecretSanta::class, mappedBy: 'owner')]
    #[Ignore]
    private Collection $secretSantas;

    /**
     * @var Collection<int, Token> $tokens
     */
    #[ORM\OneToMany(targetEntity: Token::class, mappedBy: 'user')]
    private Collection $tokens;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastActivity;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $pseudo;

    public function __construct()
    {
        $this->secretSantas = new ArrayCollection();
        $this->tokens = new ArrayCollection();
        $this->roles = [Role::USER->value];
    }

    public static function fromInvitation(string $email): User
    {
        $user = new self();

        $user
            ->setEmail($email)
            ->setIsInvited(true)
            ->setRoles([Role::GUEST]);

        return $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        if ('' === $this->email) {
            throw new LogicException('Email cannot be blank.');
        }

        return $this->email;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function hasRole(Role $role): bool
    {
        return in_array($role->value, $this->roles, true);
    }

    /**
     * @param Role[] $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = array_map(fn (Role $role) => $role->value, $roles);

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): User
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, SecretSanta>
     */
    public function getSecretSantas(): Collection
    {
        return $this->secretSantas;
    }

    /**
     * @return Collection<int, Token>
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }


    public function isInvited(): bool
    {
        return $this->isInvited;
    }

    public function setIsInvited(bool $isInvited): User
    {
        $this->isInvited = $isInvited;
        return $this;
    }

    public function getLastActivity(): \DateTimeImmutable
    {
        return $this->lastActivity;
    }

    public function setLastActivity(\DateTimeImmutable $lastActivity): void
    {
        $this->lastActivity = $lastActivity;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): void
    {
        $this->pseudo = $pseudo;
    }
}
