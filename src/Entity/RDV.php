<?php

namespace App\Entity;

use App\Repository\RDVRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RDVRepository::class)
 */
class RDV
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateRdv;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $heure;

    /**
     * @ORM\ManyToOne(targetEntity=TypeService::class, inversedBy="rDVs")
     */
    private $typeService;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRdv(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDateRdv(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(string $heure): self
    {
        $this->heure = $heure;

        return $this;
    }

    public function getTypeService(): ?TypeService
    {
        return $this->typeService;
    }

    public function setTypeService(?TypeService $typeService): self
    {
        $this->typeService = $typeService;

        return $this;
    }
}
