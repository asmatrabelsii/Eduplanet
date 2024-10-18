<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $user = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Examen $exam = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateSession = null;

    #[ORM\Column]
    private ?bool $certification = null;

    #[ORM\Column]
    private ?int $note = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExam(): ?Examen
    {
        return $this->exam;
    }

    public function setExam(?Examen $exam): self
    {
        $this->exam = $exam;

        return $this;
    }

    public function getDateSession(): ?\DateTimeInterface
    {
        return $this->dateSession;
    }

    public function setDateSession(\DateTimeInterface $dateSession): self
    {
        $this->dateSession = $dateSession;

        return $this;
    }

    public function isCertification(): ?bool
    {
        return $this->certification;
    }

    public function setCertification(bool $certification): self
    {
        $this->certification = $certification;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }
}
