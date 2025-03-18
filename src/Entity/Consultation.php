<?php

namespace App\Entity;

use App\Enum\ConsultationStatus;
use App\Repository\ConsultationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;

#[ApiResource()]
#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
#[ApiFilter(DateFilter::class, properties: ['creationDate'])]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $consultationDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reason = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Animal $animal = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    private ?User $assistant = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $veterinarian = null;

    /**
     * @var Collection<int, Treatment>
     */
    #[ORM\ManyToMany(targetEntity: Treatment::class, inversedBy: 'consultations')]
    private Collection $treatment;

    #[ORM\Column(type: Types::STRING, enumType: ConsultationStatus::class)]
    private ConsultationStatus $status;

    public function __construct()
    {
        $this->treatment = new ArrayCollection();
        $this->creationDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getConsultationDate(): ?\DateTimeInterface
    {
        return $this->consultationDate;
    }

    public function setConsultationDate(\DateTimeInterface $consultationDate): static
    {
        $this->consultationDate = $consultationDate;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }

    public function getAssistant(): ?User
    {
        return $this->assistant;
    }

    public function setAssistant(?User $assistant): static
    {
        $this->assistant = $assistant;

        return $this;
    }

    public function getVeterinarian(): ?User
    {
        return $this->veterinarian;
    }

    public function setVeterinarian(?User $veterinarian): static
    {
        $this->veterinarian = $veterinarian;

        return $this;
    }

    /**
     * @return Collection<int, Treatment>
     */
    public function getTreatment(): Collection
    {
        return $this->treatment;
    }

    public function addTreatment(Treatment $treatment): static
    {
        if (!$this->treatment->contains($treatment)) {
            $this->treatment->add($treatment);
        }

        return $this;
    }

    public function removeTreatment(Treatment $treatment): static
    {
        $this->treatment->removeElement($treatment);

        return $this;
    }

    public function getStatus(): ?ConsultationStatus
    {
        return $this->status;
    }

    public function setStatus(ConsultationStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
