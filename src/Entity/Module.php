<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ModuleRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File as FileAssert;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2, max:255, minMessage:"La libellé doit faire plus d'un caractère !", maxMessage:"La libellé ne peut pas faire plus de 255 caractères !")]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    #[FileAssert(
        mimeTypes: ["image/*", "video/mp4", "application/pdf"],
        mimeTypesMessage: "Le contenu du module doit être une vidéo, image ou pdf"
    )]
    private ?string $contenu = null;

    #[ORM\ManyToOne(inversedBy: 'modules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:20,minMessage:"La description doit faire plus de 20 caractères !")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $MimeType = null;

    #[ORM\Column]
    private ?bool $approved = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->libelle);
        }
    }

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

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->MimeType;
    }

    public function setMimeType(string $MimeType): self
    {
        $this->MimeType = $MimeType;

        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;

        return $this;
    }
}
