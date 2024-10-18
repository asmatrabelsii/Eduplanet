<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CertificationRepository;

#[ORM\Entity(repositoryClass: CertificationRepository::class)]
class Certification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'certifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $user = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $pdf = null;

    #[ORM\ManyToOne(inversedBy: 'certifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Examen $exam = null;

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

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function setPdf(string $pdf): self
    {
        $this->pdf = $pdf;

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
}
