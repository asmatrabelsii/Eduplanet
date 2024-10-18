<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Cours;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminQuizController extends AbstractController
{
    #[Route('/admin/cours/{slug}/quiz', name: 'admin_quiz_index')]
    public function index(QuizRepository $repo, Request $request, Cours $cours)
    {
        return $this->render('admin/quiz/index.html.twig', [
            'quizz' => $repo->findQuiz($request->query->getInt('page', 1),$cours)
        ]);
    }

    #[Route('/admin/cours/{slug}/quiz/new', name: 'admin_quiz_new')]
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
            $quiz->setApproved(true);
            $manager->persist($quiz);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le quiz a bien été créé !"
            );

            return $this->redirectToRoute('admin_cours_show', [
                'slug' => $cours->getSlug(),
            ]);
        }
        return $this->render('admin/quiz/new.html.twig', [
            'form' => $form->createView(),
            'slug' => $cours->getSlug(),
        ]);
    }

    #[Route('/admin/cours/{slug}/quiz/{id}/edit', name: 'admin_quiz_edit')]
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

            $manager->persist($quiz);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le quiz a bien été modifié !"
            );

            return $this->redirectToRoute('admin_cours_show', [
                'slug' => $quiz->getCours()->getSlug(),
            ]);
        }
        return $this->render('admin/quiz/edit.html.twig', [
            'form' => $form->createView(),
            'slug' => $quiz->getCours()->getSlug(),
        ]);
    }

    #[Route('/admin/cours/{slug}/quiz/{id}/delete', name: 'admin_quiz_delete')]
    public function delete(Quiz $quiz, EntityManagerInterface $manager): Response
    {
        $manager->remove($quiz);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le quiz a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_cours_show', [
            'slug' => $quiz->getCours()->getSlug(),
        ]);
    }

    #[Route('/admin/cours/{slug}/quiz/{id}/approve', name: 'admin_quiz_approve')]
    public function approve(Quiz $quiz, EntityManagerInterface $manager): Response
    {
        $quiz->setApproved(true);
        $manager->persist($quiz);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le quiz a bien été approuvé !"
        );

        return $this->redirectToRoute('admin_cours_show', [
            'slug' => $quiz->getCours()->getSlug(),
        ]);
    }

    #[Route('/admin/cours/{slug}/quiz/{id}/disapprove', name: 'admin_quiz_disapprove')]
    public function disapprove(Quiz $quiz, EntityManagerInterface $manager): Response
    {
        $quiz->setApproved(false);
        $manager->persist($quiz);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le quiz a été désapprouvé !"
        );

        return $this->redirectToRoute('admin_cours_show', [
            'slug' => $quiz->getCours()->getSlug(),
        ]);
    }
}
