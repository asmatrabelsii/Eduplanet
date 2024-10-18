<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: "Un autre utilisateur s'est déjà inscrit avec cette adresse email, merci de la modifier")]
#[UniqueEntity(fields: ['cin'], message: "CIN déjà existe")]

class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Vous devez renseigner votre nom")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Vous devez renseigner votre prénom")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message:"Vous devez renseigner un email valide")]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Vous devez renseigner votre mot de passe")]
    #[Assert\Length(min: 8, minMessage: "Votre mot de passe doit faire au moins 8 caractères")]
    private ?string $password = null;

    #[Assert\EqualTo(propertyPath: "password", message: "Vous n'avez pas tapé le même mot de passe")]
    public $passwordConfirm;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Vous devez renseigner votre CIN")]
    #[Assert\PositiveOrZero(message:"CIN incorrecte")]
    #[Assert\Length(min: 8, max: 8, exactMessage: "CIN doit contenir 8 chiffres")]
    private ?string $cin = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Cours::class)]
    private Collection $cours;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'Utilisateurs')]
    private Collection $UtilisateurRoles;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:20, minMessage:"La biographie doit faire plus de 20 caractères !")]
    private ?string $biographie = null;

    #[ORM\OneToOne(mappedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Panier $panier = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PaymentExamen::class, orphanRemoval: true)]
    private Collection $paymentExamens;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Session::class, orphanRemoval: true)]
    private Collection $sessions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Certification::class, orphanRemoval: true)]
    private Collection $certifications;

    public function getFullName()
    {
        return "{$this->prenom} {$this->nom}";
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->nom .' '. $this->prenom);
        }
    }

    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->UtilisateurRoles = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->paymentExamens = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->certifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setAuteur($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getAuteur() === $this) {
                $cour->setAuteur(null);
            }
        }

        return $this;
    }
    
    public function getUserIdentifier(): string {
        return $this->email;
    }
    
    public function getRoles(): array {
        $roles = $this->UtilisateurRoles->map(function($role) {
            return $role->getTitre();
        })->toArray();
        $roles[] = 'ROLE_APPRENANT';
        return $roles;
    }

    public function getSalt(): ?string {
        return null;
    }

    public function eraseCredentials(): void {
    }

    /**
     * @return Collection<int, Role>
     */
    public function getUtilisateurRoles(): Collection
    {
        return $this->UtilisateurRoles;
    }

    public function addUtilisateurRole(Role $utilisateurRole): self
    {
        if (!$this->UtilisateurRoles->contains($utilisateurRole)) {
            $this->UtilisateurRoles->add($utilisateurRole);
            $utilisateurRole->addUtilisateur($this);
        }

        return $this;
    }

    public function removeUtilisateurRole(Role $utilisateurRole): self
    {
        if ($this->UtilisateurRoles->removeElement($utilisateurRole)) {
            $utilisateurRole->removeUtilisateur($this);
        }

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
            $commentaire->setAuteur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getAuteur() === $this) {
                $commentaire->setAuteur(null);
            }
        }

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(string $biographie): self
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(Panier $panier)
    {
        // set the owning side of the relation if necessary
        if ($panier->getOwner() !== $this) {
            $panier->setOwner($this);
        }

        $this->panier = $panier;
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
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PaymentExamen>
     */
    public function getPaymentExamens(): Collection
    {
        return $this->paymentExamens;
    }

    public function addPaymentExamen(PaymentExamen $paymentExamen): self
    {
        if (!$this->paymentExamens->contains($paymentExamen)) {
            $this->paymentExamens->add($paymentExamen);
            $paymentExamen->setUser($this);
        }

        return $this;
    }

    public function removePaymentExamen(PaymentExamen $paymentExamen): self
    {
        if ($this->paymentExamens->removeElement($paymentExamen)) {
            // set the owning side to null (unless already changed)
            if ($paymentExamen->getUser() === $this) {
                $paymentExamen->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setUser($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getUser() === $this) {
                $session->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Certification>
     */
    public function getCertifications(): Collection
    {
        return $this->certifications;
    }

    public function addCertification(Certification $certification): self
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications->add($certification);
            $certification->setUser($this);
        }

        return $this;
    }

    public function removeCertification(Certification $certification): self
    {
        if ($this->certifications->removeElement($certification)) {
            // set the owning side to null (unless already changed)
            if ($certification->getUser() === $this) {
                $certification->setUser(null);
            }
        }

        return $this;
    }
}