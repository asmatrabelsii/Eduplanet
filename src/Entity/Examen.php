<?php

namespace App\Entity;

use App\Repository\ExamenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExamenRepository::class)]
class Examen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero(message:"Le prix doit être supérieur ou égal à 0 !")]
    private ?float $prix = null;

    #[ORM\OneToOne(inversedBy: 'examen', cascade: ['persist'])]
    private ?Cours $cours = null;

    #[ORM\OneToMany(mappedBy: 'examen', targetEntity: Question::class, orphanRemoval: true)]
    private Collection $questions;

    #[ORM\Column]
    private ?bool $approved = null;

    #[ORM\OneToMany(mappedBy: 'examen', targetEntity: PaymentExamen::class, orphanRemoval: true)]
    private Collection $paymentExamens;

    #[ORM\OneToMany(mappedBy: 'exam', targetEntity: Session::class, orphanRemoval: true)]
    private Collection $sessions;

    #[ORM\Column]
    #[Assert\Range(
        min: 55,
        max: 99,
        notInRangeMessage: 'Le barème doit être compris entre {{ min }}% et {{ max }}%',
    )]
    private ?int $bareme = null;

    #[ORM\OneToMany(mappedBy: 'exam', targetEntity: Certification::class, orphanRemoval: true)]
    private Collection $certifications;

    public function getPaymentOfUser(Utilisateur $user)
    {
        foreach ($this->paymentExamens as $payment) {
            if ($payment->getUser() === $user) return $payment;
        }

        return null;
    }

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->paymentExamens = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->certifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setExamen($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getExamen() === $this) {
                $question->setExamen(null);
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

    public function getPaymenUser(Utilisateur $user)
    {
        foreach ($this->paymentExamens as $payment) {
            if ($payment->getUser() === $user) return $user;
        }

        return null;
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
            $paymentExamen->setExamen($this);
        }

        return $this;
    }

    public function removePaymentExamen(PaymentExamen $paymentExamen): self
    {
        if ($this->paymentExamens->removeElement($paymentExamen)) {
            // set the owning side to null (unless already changed)
            if ($paymentExamen->getExamen() === $this) {
                $paymentExamen->setExamen(null);
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
            $session->setExam($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getExam() === $this) {
                $session->setExam(null);
            }
        }

        return $this;
    }

    public function getBareme(): ?int
    {
        return $this->bareme;
    }

    public function setBareme(int $bareme): self
    {
        $this->bareme = $bareme;

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
            $certification->setExam($this);
        }

        return $this;
    }

    public function removeCertification(Certification $certification): self
    {
        if ($this->certifications->removeElement($certification)) {
            // set the owning side to null (unless already changed)
            if ($certification->getExam() === $this) {
                $certification->setExam(null);
            }
        }

        return $this;
    }
}
