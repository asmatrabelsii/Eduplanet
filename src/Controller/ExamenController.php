<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;
use App\Entity\Cours;
use App\Entity\Examen;
use App\Entity\Session;
use App\Form\ExamDoType;
use App\Form\ExamenType;
use App\Entity\Certification;
use App\Entity\PaymentExamen;
use App\Entity\Utilisateur;
use App\Manager\ExamenManager;
use App\Repository\ExamenRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CertificationRepository;
use App\Repository\PaymentExamenRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExamenController extends AbstractController
{
    #[IsGranted('ROLE_APPRENANT')]
    #[Route('/paiement_examen/{id}/show', name:'paiement_examen',methods:['GET', 'POST'])]
    public function payment(Examen $examen, ExamenManager $examenManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('examen/pay.html.twig', [
            'user' => $this->getUser(),
            'intentSecret' => $examenManager->intentSecret($examen),
            'examen' => $examen
        ]);
    }

    #[IsGranted('ROLE_APPRENANT')]
    #[Route('/user/subscription/{id}/paiement_examen/load', name:'subscription_paiement_examen',methods:['GET', 'POST'])]
    public function subsciption(Examen $examen, Request $request, ExamenManager $examenManager)
    {
        $user = $this->getUser();

        if($request->getMethod() === "POST") {
            $resource = $examenManager->stripe($_POST, $examen);

            if(null !== $resource) {
                $examenManager->create_subscription($resource, $examen, $user);

                return $this->render('payment/success_examen.html.twig',[
                    'examen' => $examen
                ]);
            }
        }

        return $this->redirectToRoute('paiement_examen', ['id' => $examen->getId()]);
    }

    #[Route('/cours/{slug}/examen', name: 'examen_index')]
    public function index(ExamenRepository $repo, Cours $cours): Response
    {
        $examen = $repo->find($cours->getExamen()->getId());
        return $this->render('examen/index.html.twig', [
            'examen' => $examen,
        ]);
    }

    #[Route('/cours/{slug}/examen/new', name: 'examen_new')]
    public function new(Cours $cours, Request $request, EntityManagerInterface $manager): Response
    {
        $examen = new Examen();
        $form = $this->createForm(ExamenType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $questions = $form->get('questions')->getData();
            foreach ($questions as $question) {
                $examen->addQuestion($question);
                $question->setExamen($examen);
                $manager->persist($question);
            }

            $examen->setPrix($form->get('prix')->getData());
            $examen->setBareme($form->get('bareme')->getData());
            $examen->setCours($cours);
            $examen->setApproved(false);
            $manager->persist($examen);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'examen a bien été créé !"
            );

            return $this->redirectToRoute('cours_show', [
                'slug' => $cours->getSlug(),
            ]);
        }
        return $this->render('examen/new.html.twig', [
            'form' => $form->createView(),
            'slug' => $cours->getSlug(),
        ]);
    }

    #[Route('/cours/{slug}/examen/{id}/edit', name: 'examen_edit')]
    public function edit(Examen $examen, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ExamenType::class, $examen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $questions = $form->get('questions')->getData();
            foreach ($questions as $question) {
                $examen->addQuestion($question);
                $question->setExamen($examen);
                $manager->persist($question);
            }

            $examen->setPrix($form->get('prix')->getData());
            $examen->setBareme($form->get('bareme')->getData());
            $examen->setApproved(false);
            $manager->persist($examen);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'examen a bien été modifié !"
            );

            return $this->redirectToRoute('cours_show', [
                'slug' => $examen->getCours()->getSlug(),
            ]);
        }
        return $this->render('examen/edit.html.twig', [
            'form' => $form->createView(),
            'slug' => $examen->getCours()->getSlug(),
        ]);
    }

    #[Route('/cours/{slug}/examen/{id}/delete', name: 'examen_delete')]
    public function delete(Examen $examen, EntityManagerInterface $manager): Response
    {
        $manager->remove($examen);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'examen a été supprimé !"
        );

        return $this->redirectToRoute('cours_show', [
            'slug' => $examen->getCours()->getSlug(),
        ]);
    }

    #[Security('is_granted("ROLE_APPRENANT") and user === examen.getPaymenUser(user)')]
    #[Route('/Exam/{id}/do', name: 'Exam_do')]
    public function do(Examen $examen, ExamenRepository $examenRepository, Request $request, EntityManagerInterface $manager, PaymentExamenRepository $repo, SluggerInterface $slugger)
    {
        $session = new Session();
        $examen = $examenRepository->findOneById($examen->getId());
        $paymentExam = $repo->findOneByExamen($examen);
        $pay = new PaymentExamen();
        
        foreach ($this->getUser()->getPaymentExamens() as $payment) {
            if ($payment === $paymentExam) {
                $pay = $payment;
            }
        }
        $session->setExam($examen);
        $session->setUser($this->getUser());
        $session->setDateSession(new Datetime());

        $form = $this->createForm(ExamDoType::class, null, [
            'questions' => $examen->getQuestions(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $score = 0;
            foreach ($examen->getQuestions() as $question){
                if ($data[$question->getId()] === $question->getChoix1()){
                    $score++;
                }
            }
            $session->setNote($score);

            if ($score >= round(($examen->getBareme() * count($examen->getQuestions()) / 100))) {
                $session->setCertification(true);
                $examen->removePaymentExamen($pay);
                $this->getUser()->removePaymentExamen($pay);
                $manager->remove($pay);
                $manager->flush();
                $manager->persist($session);
                $manager->flush();
                $pdf = $this->generateCertification($this->getUser()->getFullName(), $examen);

                $originalFilename = 'certification.pdf';
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.pdf';
                $pdfFilePath = $this->getParameter('pdf_directory').'/'.$newFilename;
                file_put_contents($pdfFilePath, $pdf);

                $certification = new Certification();
                $certification->setUser($this->getUser());
                $certification->setPdf($newFilename);
                $certification->setExam($examen);
                $manager->persist($certification);
                $manager->flush();

                return $this->render('examen/success.html.twig', [
                    'certification' => $certification,
                ]);
            } else {
                $examen->removePaymentExamen($pay);
                $this->getUser()->removePaymentExamen($pay);
                $manager->remove($pay);
                $manager->flush();
                $session->setCertification(false);
                $manager->persist($session);
                $manager->flush();
                return $this->render('examen/failure.html.twig', [
                    'score' => $score,
                ]);
            }
        }

        return $this->render('examen/examen.html.twig', [
            'form' => $form->createView(),
            'time' => count($examen->getQuestions()),
        ]);
    }

    #[Route('/Exam/expired', name: 'Exam_expired')]
    public function expired()
    {
        return $this->render('examen/expired-page.html.twig');
    }

    private function generateCertification($studentName, $exam)
    {
        $dompdf = new Dompdf();

        $logoPath = 'logof.png';
        $logoData = file_get_contents($logoPath);
        $logoBase64 = base64_encode($logoData);

        $signaturePath = 'Eduplanet_cocosign.png';
        $signatureData = file_get_contents($signaturePath);
        $signatureBase64 = base64_encode($signatureData);

        $borderPath = 'border.jpg';
        $borderData = file_get_contents($borderPath);
        $borderBase64 = base64_encode($borderData);

        $html = $this->renderView('certification/index.html.twig', [
            'student_name' => $studentName,
            'exam_name' => $exam,
            'date' => new DateTime(),
            'logo_base64' => $logoBase64,
            'signature_base64' => $signatureBase64,
            'border_base64' => $borderBase64,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }
    
    #[Route('/certification/{id}/download', name: 'certification_download')]
    public function downloadCertification($id, CertificationRepository $certificationRepository)
    {
        $certification = $certificationRepository->find($id);

        if (!$certification) {
            throw $this->createNotFoundException('Certification not found');
        }

        $filePath = $this->getParameter('pdf_directory').'/'.$certification->getPdf();

        return new BinaryFileResponse($filePath);
    }

    #[Route('/compte/{id}/mes_certifs', name: 'certification_index')]
    public function MesCertifs(Utilisateur $utilisateur)
    {
        return $this->render('user/certif.html.twig', [
            'certifs' => $utilisateur->getCertifications(),
        ]);
    }
}
