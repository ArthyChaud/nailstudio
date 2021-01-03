<?php

namespace App\Entity;

use App\Kernel;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 */
class Media
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
    private $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity=CarouselLike::class, inversedBy="medias")
     */
    private $carouselLike;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @ORM\PreRemove()
     * @throws Exception
     */
    public function removeUpload()
    {
        try {
            $kernel = new Kernel('', true);
            unlink($kernel->getProjectDir().'/public/uploads/carousel_like/'.$this->getPath());
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getCarouselLike(): ?CarouselLike
    {
        return $this->carouselLike;
    }

    public function setCarouselLike(?CarouselLike $carouselLike): self
    {
        $this->carouselLike = $carouselLike;

        return $this;
    }
}
