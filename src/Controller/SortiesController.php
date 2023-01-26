<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\CreerSortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortiesController extends AbstractController
{
    /**
     * @Route("/sorties/list", name="sorties_list")
     */
    public function index(): Response
    {
        return $this->render('sorties/list.html.twig');
    }

    /**
     * @Route("/sorties/creerSortie", name="sorties_creerSortie")
     */
    public function creerSortie(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository, ParticipantRepository $participantRepository): Response
    {
        $sortie = new Sortie();
        $user =$this->getUser();
        $sortie->setParticipantOrganisateur($user);
        $sortie->setCampus($user->getCampus());
        $etats = $etatRepository ->findAll();
        $creerSortieForm = $this->createForm(CreerSortieType::class, $sortie);

        $creerSortieForm->handleRequest($request);

        if ($creerSortieForm->get('enregistrer')->isClicked()) {
            $sortie->setEtat($etats[0]);
            $message = 'Votre sortie a été créée avec succès';
        }elseif ($creerSortieForm->get('publier')->isClicked()){
            $sortie->setEtat(($etats[1]));
            $message = 'Votre sortie a été publiée avec succès';
        }

        if ($creerSortieForm->isSubmitted() && $creerSortieForm->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addflash('success', $message);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('sorties/creerSortie.html.twig', [
            "user" => $user,
            "creerSortieForm" => $creerSortieForm->createView()
        ]);
    }
}
