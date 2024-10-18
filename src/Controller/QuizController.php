<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Cours;
use App\Form\QuizType;
use App\Form\QuizDoType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizController extends AbstractController
{
    #[Route('/cours/{slug}/quiz', name: 'quiz_index')]
    public function index(QuizRepository $repo, Request $request, Cours $cours): Response
    {
        $quizz = $repo->findQuiz($request->query->getInt('page', 1),$cours);
        return $this->render('quiz/index.html.twig', [
            'quizz'=> $quizz
        ]);
    }

    #[Route('/cours/{slug}/quiz/new', name: 'quiz_new')]
    public function new(Cours $cours, Request $request, EntityManagerInterface $manager): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $quizQuestions = $form->get('quizQuestions')->getData();
            foreach ($quizQuestions as $quizQuestions) {
                $quiz->addQuizQuestion($quizQuestions);
                $quizQuestions->setQuiz($quiz);
                $manager->persist($quizQuestions);
            }

            $quiz->setCours($cours);
            $quiz->setApproved(false);
            $manager->persist($quiz);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le quiz a bien été créé !"
            );

            return $this->redirectToRoute('cours_show', [
                'slug' => $cours->getSlug(),
            ]);
        }
        return $this->render('quiz/new.html.twig', [
            'form' => $form->createView(),
            'slug' => $cours->getSlug(),
        ]);
    }

    #[Route('/cours/{slug}/quiz/{id}/edit', name: 'quiz_edit')]
    public function edit(Quiz $quiz, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $quizQuestions = $form->get('quizQuestions')->getData();
            foreach ($quizQuestions as $quizQuestions) {
                $quiz->addQuizQuestion($quizQuestions);
                $quizQuestions->setQuiz($quiz);
                $manager->persist($quizQuestions);
            }

            $quiz->setApproved(false);
            $manager->persist($quiz);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le quiz a bien été modifié !"
            );

            return $this->redirectToRoute('cours_show', [
                'slug' => $quiz->getCours()->getSlug(),
            ]);
        }
        return $this->render('quiz/edit.html.twig', [
            'form' => $form->createView(),
            'slug' => $quiz->getCours()->getSlug(),
        ]);
    }

    #[Route('/cours/{slug}/quiz/{id}/delete', name: 'quiz_delete')]
    public function delete(Quiz $quiz, EntityManagerInterface $manager): Response
    {
        $manager->remove($quiz);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le quiz a été supprimé !"
        );

        return $this->redirectToRoute('cours_show', [
            'slug' => $quiz->getCours()->getSlug(),
        ]);
    }

    #[Route('/quiz/{id}/do', name: 'quiz_do')]
    public function do(Quiz $quiz, QuizRepository $quizRepository, Request $request)
    {
        $quiz = $quizRepository->findOneById($quiz->getId());

        $form = $this->createForm(QuizDoType::class, null, [
            'questions' => $quiz->getQuizQuestions(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $score = 0;
            foreach ($quiz->getQuizQuestions() as $question){
                if ($data[$question->getId()] === $question->getChoix4()){
                    $score++;
                }
            }

            return $this->render('quiz/result.html.twig', [
                'score' => $score,
                'questions' => $quiz->getQuizQuestions(),
            ]);
        }

        return $this->render('quiz/quiz.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
