<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\CreerSortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
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
    public function creerSortie(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $user =$this->getUser();
        $sortie->setParticipantOrganisateur($user);
        $sortie->setCampus($user->getCampus());
        $etatCreee = $etatRepository->findOneBy(["libelle"=>"Créée"]);
        $etatOuverte = $etatRepository->findOneBy(["libelle"=>"Ouverte"]);
        $creerSortieForm = $this->createForm(CreerSortieType::class, $sortie);

        $creerSortieForm->handleRequest($request);

        if ($creerSortieForm->get('enregistrer')->isClicked()) {
            $sortie->setEtat($etatCreee);
            $message = 'Votre sortie a été créée avec succès';
        }elseif ($creerSortieForm->get('publier')->isClicked()){
            $sortie->setEtat($etatOuverte);
            $message = 'Votre sortie a été publiée avec succès';
        }

        if ($creerSortieForm->isSubmitted() && $creerSortieForm->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addflash('success', $message);
            return $this->redirectToRoute('home');
        }

        return $this->render('sorties/creerSortie.html.twig', [
            "user" => $user,
            "creerSortieForm" => $creerSortieForm->createView()
        ]);
    }

    /**
     * @Route("/sorties/modifierSortie", name="sorties_modifierSortie")
     */
    public function modifierSortie(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, int $id): Response
    {
        $sortie = $sortieRepository->find($id);
        $etat = $sortie->getEtat()->getLibelle();
        $organisateur = $sortie->getParticipantOrganisateur() === $this->getUser();

        if ($organisateur && $sortie != null && $etat == "Créée"){
            $modifierSortieForm = $this->createForm(CreerSortieType::class, $sortie);
            $modifierSortieForm->handleRequest($request);

            if ($modifierSortieForm->isSubmitted() and $modifierSortieForm->isValid() ) {
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a bien été modifiée.');
                return $this->redirectToRoute('home');
            }
        }else{
            $this->addFlash('error','Vous ne pouvez pas modifier cette sortie.');
            return $this->redirectToRoute('home');
        }

        return $this->render('sorties/modifierSortie.html.twig', [
            "modifierSortieForm" => $modifierSortieForm->createView()
        ]);
    }
}

