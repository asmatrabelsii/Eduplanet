<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $approved = null;

    #[ORM\ManyToOne(inversedBy: 'quizz')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: QuizQuestion::class, orphanRemoval: true)]
    private Collection $quizQuestions;

    public function __construct()
    {
        $this->quizQuestions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, QuizQuestion>
     */
    public function getQuizQuestions(): Collection
    {
        return $this->quizQuestions;
    }

    public function addQuizQuestion(QuizQuestion $quizQuestion): self
    {
        if (!$this->quizQuestions->contains($quizQuestion)) {
            $this->quizQuestions->add($quizQuestion);
            $quizQuestion->setQuiz($this);
        }

        return $this;
    }

    public function removeQuizQuestion(QuizQuestion $quizQuestion): self
    {
        if ($this->quizQuestions->removeElement($quizQuestion)) {
            // set the owning side to null (unless already changed)
            if ($quizQuestion->getQuiz() === $this) {
                $quizQuestion->setQuiz(null);
            }
        }

        return $this;
    }
}
