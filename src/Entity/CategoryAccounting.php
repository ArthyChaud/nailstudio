<?php

namespace App\Entity;

use App\Repository\CategoryAccountingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryAccountingRepository::class)
 */
class CategoryAccounting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity=Accounting::class, mappedBy="CategoryAccounting")
     */
    private $accountings;

    public function __construct()
    {
        $this->accountings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection|Accounting[]
     */
    public function getAccountings(): Collection
    {
        return $this->accountings;
    }

    public function addAccounting(Accounting $accounting): self
    {
        if (!$this->accountings->contains($accounting)) {
            $this->accountings[] = $accounting;
            $accounting->setCategoryAccounting($this);
        }

        return $this;
    }

    public function removeAccounting(Accounting $accounting): self
    {
        if ($this->accountings->contains($accounting)) {
            $this->accountings->removeElement($accounting);
            // set the owning side to null (unless already changed)
            if ($accounting->getCategoryAccounting() === $this) {
                $accounting->setCategoryAccounting(null);
            }
        }

        return $this;
    }
}
