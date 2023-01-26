<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CreerSortieType;
use App\Form\SearchType;
use App\Model\Search;
use App\Form\ModifierSortieType;
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
    public function sorties_list(ParticipantRepository $participantRepository, SortieRepository $sortieRepository, Request $request): Response
    {

        // TODO : récupérer le participant connecté
        $participant = $this->getUser();
        //$participant = $participantRepository->loadUserByIdentifier($this"JMO");

        $search = new Search();
        $search->setCampus($participant->getCampus());

        $searchForm = $this->createForm(SearchType::class,$search);
        $searchForm->handleRequest($request);

        $sorties = $sortieRepository->findSortiesWithFilter($search,$participant);

        return $this->render('sorties/list.html.twig',[
            'sorties'=>$sorties,
            'user'=>$participant,
            'searchForm'=>$searchForm->createView()
        ]);
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
            return $this->redirectToRoute('sorties_list');
        }

        return $this->render('sorties/creerSortie.html.twig', [
            "user" => $user,
            "creerSortieForm" => $creerSortieForm->createView()
        ]);
    }

    /**
     * @Route("/sorties/modifierSortie/{id}", name="sorties_modifierSortie", requirements={"id"="\d+"})
     */
    public function modifierSortie(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatRepository $etatRepository, int $id): Response
    {
        $sortie = $sortieRepository->find($id);
        $user =$this->getUser();
        $etat = $sortie->getEtat()->getLibelle();
        $etatCreee = $etatRepository->findOneBy(["libelle"=>"Créée"]);
        $etatOuverte = $etatRepository->findOneBy(["libelle"=>"Ouverte"]);
        $organisateur = $sortie->getParticipantOrganisateur() === $this->getUser();

        if ($organisateur && $sortie != null && $etat == "Créée"){
            $modifierSortieForm = $this->createForm(ModifierSortieType::class, $sortie);
            $modifierSortieForm->handleRequest($request);

            if ($modifierSortieForm->get('enregistrer')->isClicked()) {
                $sortie->setEtat($etatCreee);
                $message = 'Votre sortie a été créée avec succès';
            }elseif ($modifierSortieForm->get('publier')->isClicked()){
                $sortie->setEtat($etatOuverte);
                $message = 'Votre sortie a été publiée avec succès';
            }

            if ($modifierSortieForm->isSubmitted() and $modifierSortieForm->isValid() ) {
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', $message);
                return $this->redirectToRoute('home');
            }
        }else{
            $this->addFlash('error','Vous ne pouvez pas modifier cette sortie.');
            return $this->redirectToRoute('home');
        }

        return $this->render('sorties/modifierSortie.html.twig', [
            "user" => $user,
            "modifierSortieForm" => $modifierSortieForm->createView()
        ]);
    }
}

