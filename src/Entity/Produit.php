<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
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
    private $libelle;

    /**
     * @ORM\ManyToOne(targetEntity=TypeProduit::class, inversedBy="produits")
     */
    private $idTypeProduit;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\Column(type="integer")
     */
    private $besoin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getIdTypeProduit(): ?TypeProduit
    {
        return $this->idTypeProduit;
    }

    public function setIdTypeProduit(?TypeProduit $idTypeProduit): self
    {
        $this->idTypeProduit = $idTypeProduit;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getBesoin(): ?int
    {
        return $this->besoin;
    }

    public function setBesoin(int $besoin): self
    {
        $this->besoin = $besoin;

        return $this;
    }
}
