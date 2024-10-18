<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use App\Entity\Cathegories;
use App\Entity\Utilisateur;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CoursRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\File as FileAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: ['titre'],
    message: "Ce titre existe déjà; merci de le modifier !"
)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2, max:255, minMessage:"Le titre doit faire plus d'un caractère !", maxMessage:"Le titre ne peut pas faire plus de 255 caractères !")]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:20, minMessage:"La description doit faire plus de 20 caractères !")]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 0,
        max: 3000,
        notInRangeMessage: 'Le prix doit être compris entre {{ min }}TND et {{ max }}TND',
    )]
    private ?float $prix = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Module::class, orphanRemoval: true)]
    #[Assert\Valid()]
    private Collection $modules;

    #[ORM\Column(length: 255)]
    #[FileAssert(
        mimeTypes: ["image/*", "video/mp4"],
        mimeTypesMessage: "Merci d'ajouter une image ou une vidéo comme couverture"
    )]
    private ?string $coverImage = null;

    #[ORM\ManyToOne (targetEntity: Cathegories::class, inversedBy:"cours")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cathegories $cathegorie = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $auteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\Column]
    private ?bool $approved = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Criteres::class, orphanRemoval: true)]
    private Collection $criteres;

    #[ORM\OneToOne(mappedBy: 'cours', cascade: ['persist', 'remove'])]
    private ?Examen $examen = null;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Quiz::class, orphanRemoval: true)]
    private Collection $quizz;

    #[ORM\ManyToMany(targetEntity: Panier::class, mappedBy: 'cours')]
    private Collection $paniers;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Payment::class)]
    private Collection $payments;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->criteres = new ArrayCollection();
        $this->quizz = new ArrayCollection();
        $this->paniers = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->titre);
        }
    }

    public function getCommentFromAuteur(Utilisateur $auteur)
    {
        foreach ($this->commentaires as $comment) {
            if ($comment->getAuteur() === $auteur) return $comment;
        }

        return null;
    }

    public function getAvgRatings()
    {
        $sum = array_reduce($this->commentaires->toArray(), function($total, $comment){
            return $total + $comment->getRating();
        }, 0);

        if(count($this->commentaires) > 0) return $sum / count($this->commentaires);

        return 0;
    }

    public function getCommentFromAuthor(Utilisateur $author)
    {
        foreach ($this->commentaires as $comment) {
            if ($comment->getAuteur() === $author) return $comment;
        }

        return null;
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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->setCours($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->removeElement($module)) {
            // set the owning side to null (unless already changed)
            if ($module->getCours() === $this) {
                $module->setCours(null);
            }
        }

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getCathegorie(): ?Cathegories
    {
        return $this->cathegorie;
    }

    public function setCathegorie(?Cathegories $cathegorie): self
    {
        $this->cathegorie = $cathegorie;

        return $this;
    }

    public function getAuteur(): ?Utilisateur
    {
        return $this->auteur;
    }

    public function setAuteur(?Utilisateur $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setCours($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getCours() === $this) {
                $commentaire->setCours(null);
            }
        }

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

    /**
     * @return Collection<int, Criteres>
     */
    public function getCriteres(): Collection
    {
        return $this->criteres;
    }

    public function addCritere(Criteres $critere): self
    {
        if (!$this->criteres->contains($critere)) {
            $this->criteres->add($critere);
            $critere->setCours($this);
        }

        return $this;
    }

    public function removeCritere(Criteres $critere): self
    {
        if ($this->criteres->removeElement($critere)) {
            // set the owning side to null (unless already changed)
            if ($critere->getCours() === $this) {
                $critere->setCours(null);
            }
        }

        return $this;
    }

    public function getExamen(): ?Examen
    {
        return $this->examen;
    }

    public function setExamen(?Examen $examen): self
    {
        // unset the owning side of the relation if necessary
        if ($examen === null && $this->examen !== null) {
            $this->examen->setCours(null);
        }

        // set the owning side of the relation if necessary
        if ($examen !== null && $examen->getCours() !== $this) {
            $examen->setCours($this);
        }

        $this->examen = $examen;

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizz(): Collection
    {
        return $this->quizz;
    }

    public function addQuizz(Quiz $quizz): self
    {
        if (!$this->quizz->contains($quizz)) {
            $this->quizz->add($quizz);
            $quizz->setCours($this);
        }

        return $this;
    }

    public function removeQuizz(Quiz $quizz): self
    {
        if ($this->quizz->removeElement($quizz)) {
            // set the owning side to null (unless already changed)
            if ($quizz->getCours() === $this) {
                $quizz->setCours(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Panier>
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers->add($panier);
            $panier->addCour($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->removeElement($panier)) {
            $panier->removeCour($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setCours($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getCours() === $this) {
                $payment->setCours(null);
            }
        }

        return $this;
    }
}
