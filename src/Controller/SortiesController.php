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
        //$user =$this->getUser();
        //$sortie->setParticipantOrganisateur($user);
        //$sortie->setCampus($user->getCampus());
        $etats = $etatRepository ->findAll();
        $participantConnecte = $participantRepository->findOneBy(["nom" => "cabassut"]); // TODO: à changer pour récupérer l'utilisateur connecté
        $creerSortieForm = $this->createForm(CreerSortieType::class, $sortie);

        $creerSortieForm->handleRequest($request);

        if ($creerSortieForm->get('enregistrer')->isClicked()) {
            $sortie->setEtat($etats[0]);
        }elseif ($creerSortieForm->get('publier')->isClicked()){
            $sortie->setEtat(($etats[1]));
        }

        if ($creerSortieForm->isSubmitted() && $creerSortieForm->isValid()){
            $sortie->setParticipantOrganisateur($participantConnecte);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addflash('success','Votre sortie a été créée avec succès');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('sorties/creerSortie.html.twig', [
            "creerSortieForm" => $creerSortieForm->createView()
        ]);
    }
}
