<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Client;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['owner' => 'exact'])]
#[Get(
    security: "is_granted('ROLE_ASSISTANT')",
    securityMessage: 'Only assistants can see animals.'
)]
#[GetCollection(
    security: "is_granted('ROLE_ASSISTANT')",
    securityMessage: 'Only assistants can list animals.'
)]
#[Post(
    security: "is_granted('ROLE_ASSISTANT')",
    securityMessage: 'Only assistants can create animals.'
)]
#[Put(
    security: "is_granted('ROLE_ASSISTANT')",
    securityMessage: 'Only assistants can edit animals.'
)]
#[Patch(
    security: "is_granted('ROLE_ASSISTANT')",
    securityMessage: 'Only assistants can edit animals.'
)]
#[Delete(
    security: "is_granted('ROLE_ASSISTANT')",
    securityMessage: 'Only assistants can delete animals.'
)]
class Animal
{
    #[Groups('read')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['read', 'write'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['read', 'write'])]
    #[ORM\Column(length: 255)]
    private ?string $species = null;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $birthdate = null;

    #[Groups(['read', 'write'])]
    #[ORM\OneToOne(inversedBy: 'animal', cascade: ['persist', 'remove'])]
    private ?Media $photo = null;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'animal', orphanRemoval: true)]
    private Collection $consultations;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['read', 'write'])]
    private ?Client $owner = null;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSpecies(): ?string
    {
        return $this->species;
    }

    public function setSpecies(string $species): static
    {
        $this->species = $species;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getPhoto(): ?Media
    {
        return $this->photo;
    }

    public function setPhoto(?Media $photo): static
    {
        $this->photo = $photo;

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
            $consultation->setAnimal($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): static
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getAnimal() === $this) {
                $consultation->setAnimal(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?Client
    {
        return $this->owner;
    }

    public function setOwner(?Client $owner): static
    {
        $this->owner = $owner;
        return $this;
    }
}
