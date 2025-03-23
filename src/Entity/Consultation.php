<?php

namespace App\Entity;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\ConsultationStatus;
use App\Enum\ConsultationBuyStatus;
use App\Repository\ConsultationRepository;
use App\State\ConsultationStatusCheckerProcessor;
use App\State\ConsultationVeterinarianAttributionProcessor;
use App\State\ChainConsultationProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Filter\TodayFilter;

#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
#[Get(security: "is_granted('ROLE_ASSISTANT')")]
#[GetCollection(security: "is_granted('ROLE_ASSISTANT') or is_granted('ROLE_VETERINARIAN')")]
#[Patch(
    processor: ChainConsultationProcessor::class,
    security: "is_granted('ROLE_ASSISTANT') or is_granted('ROLE_VETERINARIAN')"
)]
#[Delete(security: "is_granted('ROLE_ASSISTANT')")]
#[Post(security: "is_granted('ROLE_ASSISTANT')")]
#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
#[ApiFilter(DateFilter::class, properties: ['creationDate', 'consultationDate'])]
#[ApiFilter(TodayFilter::class)]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['read'])]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['read', 'write'])]
    private ?\DateTimeInterface $consultationDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read', 'write'])]
    private ?string $reason = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?Animal $animal = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[Groups(['read', 'write'])]
    private ?User $assistant = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?User $veterinarian = null;

    /**
     * @var Collection<int, Treatment>
     */
    #[ORM\ManyToMany(targetEntity: Treatment::class, inversedBy: 'consultations')]
    #[Groups(['read', 'write'])]
    private Collection $treatment;

    #[ORM\Column(type: Types::STRING, enumType: ConsultationStatus::class)]
    #[Groups(['read', 'write'])]
    private ConsultationStatus $status;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Groups(['read', 'write'])]
    private ?float $priceClient = null;

    #[ORM\Column(type: Types::STRING, enumType: ConsultationBuyStatus::class)]
    #[Groups(['read'])]
    private ConsultationBuyStatus $buyStatus = ConsultationBuyStatus::REMAINED;

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
            $this->updateBuyStatus();
        }

        return $this;
    }

    public function removeTreatment(Treatment $treatment): static
    {
        if ($this->treatment->removeElement($treatment)) {
            $this->updateBuyStatus();
        }

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

    public function getPriceClient(): ?float
    {
        return $this->priceClient;
    }

    public function setPriceClient(?float $priceClient): static
    {
        $this->priceClient = $priceClient;
        $this->updateBuyStatus();
        return $this;
    }

    public function getBuyStatus(): ConsultationBuyStatus
    {
        return $this->buyStatus;
    }

    private function updateBuyStatus(): void
    {
        $totalTreatmentsPrice = 0;
        foreach ($this->treatment as $treatment) {
            $totalTreatmentsPrice += $treatment->getPrice();
        }

        $this->buyStatus = ($this->priceClient !== null && $this->priceClient >= $totalTreatmentsPrice)
            ? ConsultationBuyStatus::PAID
            : ConsultationBuyStatus::REMAINED;
    }
}
