<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:20, minMessage:"L'enoncé doit faire plus de 20 caractères !")]
    private ?string $enonce = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2, max:255, minMessage:"La réponce doit faire plus d'un caractère !", maxMessage:"La réponce ne peut pas faire plus de 255 caractères !")]
    private ?string $choix1 = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2, max:255, minMessage:"La réponce doit faire plus d'un caractère !", maxMessage:"La réponce ne peut pas faire plus de 255 caractères !")]
    private ?string $choix2 = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2, max:255, minMessage:"La réponce doit faire plus d'un caractère !", maxMessage:"La réponce ne peut pas faire plus de 255 caractères !")]
    private ?string $choix3 = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2, max:255, minMessage:"La réponce doit faire plus d'un caractère !", maxMessage:"La réponce ne peut pas faire plus de 255 caractères !")]
    private ?string $choix4 = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Examen $examen = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnonce(): ?string
    {
        return $this->enonce;
    }

    public function setEnonce(string $enonce): self
    {
        $this->enonce = $enonce;

        return $this;
    }

    public function getChoix1(): ?string
    {
        return $this->choix1;
    }

    public function setChoix1(string $choix1): self
    {
        $this->choix1 = $choix1;

        return $this;
    }

    public function getChoix2(): ?string
    {
        return $this->choix2;
    }

    public function setChoix2(string $choix2): self
    {
        $this->choix2 = $choix2;

        return $this;
    }

    public function getChoix3(): ?string
    {
        return $this->choix3;
    }

    public function setChoix3(string $choix3): self
    {
        $this->choix3 = $choix3;

        return $this;
    }

    public function getChoix4(): ?string
    {
        return $this->choix4;
    }

    public function setChoix4(string $choix4): self
    {
        $this->choix4 = $choix4;

        return $this;
    }

    public function getExamen(): ?Examen
    {
        return $this->examen;
    }

    public function setExamen(?Examen $examen): self
    {
        $this->examen = $examen;

        return $this;
    }
}
