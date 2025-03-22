<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\State\UserPasswordHasherProcessor;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Enum\UserRole;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_DIRECTOR')", securityMessage: 'You are not allowed to get users'),
        new Post(
            security: "is_granted('ROLE_DIRECTOR') or !is_granted('IS_AUTHENTICATED')", 
            securityMessage: 'You are not allowed to create an user',
            processor: UserPasswordHasherProcessor::class
        ),
        new Get(security: "is_granted('ROLE_DIRECTOR') or object == user", securityMessage: 'You are not allowed to get this user'),
        new Put(processor: UserPasswordHasherProcessor::class, security: "is_granted('ROLE_DIRECTOR') or object == user", securityMessage: 'You are not allowed to edit this user'),
        new Patch(processor: UserPasswordHasherProcessor::class, security: "is_granted('ROLE_DIRECTOR') or object == user", securityMessage: 'You are not allowed to edit this user'),
        new Delete(security: "is_granted('ROLE_DIRECTOR') or object == user", securityMessage: 'You are not allowed to delete this user'),
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups('read')]
    private ?string $password = null;

    #[Groups('write')]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $lastname = null;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'assistant')]
    private Collection $consultations;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
        $this->roles = [UserRole::getDefaultRole()];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = UserRole::ROLE_USER->value;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
 
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): static
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations->add($consultation);
            $consultation->setAssistant($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): static
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getAssistant() === $this) {
                $consultation->setAssistant(null);
            }
        }

        return $this;
    }
}
