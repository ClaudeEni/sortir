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
     * @Route("/sorties/creerSortie", name="/sorties_creerSortie")
     */
    public function creerSortie(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository, ParticipantRepository $participantRepository): Response
    {
        $sortie = new Sortie();
        $etatCree = $etatRepository->findOneBy(["libelle" => "Créée"]); // TODO: Les BDD de tous les utilisateurs doivent posséder les mêmes libellés d'états
        $participantConnecte = $participantRepository->findOneBy(["nom" => "cabassut"]); // TODO: à changer pour récupérer l'utilisateur connecté
        $creerSortieForm = $this->createForm(CreerSortieType::class, $sortie);

        $creerSortieForm->handleRequest($request);

        if ($creerSortieForm->isSubmitted() && $creerSortieForm->isValid()){
            $sortie->setEtat($etatCree);
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
