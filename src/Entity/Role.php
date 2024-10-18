<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, inversedBy: 'UtilisateurRoles')]
    private Collection $Utilisateurs;

    public function __construct()
    {
        $this->Utilisateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateurs(): Collection
    {
        return $this->Utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->Utilisateurs->contains($utilisateur)) {
            $this->Utilisateurs->add($utilisateur);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        $this->Utilisateurs->removeElement($utilisateur);

        return $this;
    }
}
