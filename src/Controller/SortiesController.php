<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulerSortieType;
use App\Form\CreerSortieType;
use App\Form\SearchType;
use App\Model\Search;
use App\Form\ModifierSortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

class SortiesController extends AbstractController
{
    /**
     * @Route("/sorties/list", name="sorties_list")
     */
    public function sorties_list(ParticipantRepository $participantRepository, SortieRepository $sortieRepository, Request $request): Response
    {
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
                return $this->redirectToRoute('sorties_list');
            }
        }else{
            $this->addFlash('error','Vous ne pouvez pas modifier cette sortie.');
            return $this->redirectToRoute('sorties_list');
        }

        return $this->render('sorties/modifierSortie.html.twig', [
            "user" => $user,
            "modifierSortieForm" => $modifierSortieForm->createView()
        ]);
    }

    /**
     * @Route("/sorties/supprimerSortie/{id}", name="sorties_supprimerSortie", requirements={"id"="\d+"})
     */
    public function supprimerSortie(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatRepository $etatRepository, int $id): Response
    {
        $sortie = $sortieRepository->find($id);
        $organisateur = $sortie->getParticipantOrganisateur() === $this->getUser();
        $etat = $sortie->getEtat()->getLibelle();

        if ($organisateur && $etat == "Créée"){
            if (!$sortie) {
                $this->addFlash('error','Vous ne pouvez pas supprimer cette sortie.');
                return $this->redirectToRoute('sorties_list');
            }else{
                $entityManager->remove($sortie);
                $entityManager->flush();
                $this->addFlash('success','Votre sortie a été supprimée avec succès.');
                return $this->redirectToRoute('sorties_list');
            }
        }

        return $this->redirectToRoute('sorties_list');
    }


    /**
     * @Route("/sorties/annulerSortie/{id}", name="sorties_annulerSortie", requirements={"id"="\d+"})
     */
    public function annulerSortie($id,
                                  Request $request,
                                  EntityManagerInterface $entityManager,
                                  SortieRepository $sortieRepository,
                                  EtatRepository $etatRepository,
                                  AnnulerSortieType $annulerSortieType): Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie){
            throw $this->createNotFoundException('Cette sortie n\'est pas valide !');
        }

        $annulerSortieForm = $this->createForm(AnnulerSortieType::class,$sortie);
        $annulerSortieForm->handleRequest($request);

        if($annulerSortieForm->isSubmitted() && $annulerSortieForm->isValid()){
            if ($annulerSortieForm->get('save')->isClicked()){
                //récupérarion de l'état "Annulée" pour le mettre à la sortie
                $etatAnnulee = $etatRepository->findOneBy(["libelle"=>"Annulée"]);
                $sortie->setEtat($etatAnnulee);

                $entityManager->persist($sortie);
                $entityManager->flush();

                $message="La sortie ".$sortie->getNom()." a bien été annulée avec succès";
                $this->addflash('success', $message);
            }
            return $this->redirectToRoute('sorties_list');
        }

        return $this->render('sorties/annulerSortie.html.twig',[
            'sortie'=>$sortie,
            'annulerSortieForm'=>$annulerSortieForm->createView()
        ]);

    }

    /**
     * @Route ("sorties/inscrire/{id}",name="sortie_inscription_sortie")
     */
    public function inscriptionSortie($id,
                                    SortieRepository $sortieRepository,
                                    EtatRepository $etatRepository,
                                    EntityManagerInterface $entityManager,
                                    Request $request) : Response
    {

        $sortie = $sortieRepository->find($id);
        $etatOuverte = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
        $etatCloturee = $etatRepository->findOneBy(['libelle' => 'Clôturée']);
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'est pas valide !');
        }

        // on ne peut s'inscrire si la sortie est ouverte
        if ($sortie->getEtat() != $etatOuverte) {
            $message = "La sortie " . $sortie->getNom() . " n\'est pas ouverte, l\'inscription n\'est pas possible";
            $this->addflash('success', $message);
        } // et avec de la place
        elseif ($sortie->getParticipants()->count() == $sortie->getNbInscriptionsMax()) {
            $message = "Le nombre maximum d\'inscrit est atteint, l\'inscription à la sortie " . $sortie->getNom() . "n\'est pas possible";
            $this->addflash('success', $message);
        } // inscription possible
        else {
            $sortie->addParticipant($this->getUser());
            $entityManager->persist($sortie);
            $entityManager->flush();

            // si le nombre max de participant est atteint, on cloture la sortie
            $sortie = $sortieRepository->find($id);
            if ($sortie->getNbInscriptionsMax()==$sortie->getParticipants()->count()){
                $sortie->setEtat($etatCloturee);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }
        };
        return $this->redirectToRoute('sorties_list');
    }

    /**
     * @Route ("sorties/sedesister/{id}",name="sortie_sedesister")
     */
    public function seDesisterSortie($id, SortieRepository $sortieRepository,
                                        EtatRepository $etatRepository,
                                        EntityManagerInterface $entityManager) : Response {
        $sortie = $sortieRepository->find($id);
        $etatOuverte = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
        $etatCloture = $etatRepository->findOneBy(['libelle' => 'Clôturée']);
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'est pas valide !');
        }
        // on ne peut se désinscrire que si la sortie est ouverte
        if ($sortie->getEtat() != $etatOuverte and $sortie->getEtat() != $etatCloture) {
            $message = "La sortie " . $sortie->getNom() . " n\'est ni ouverte ni clôturée, la désinscription n\'est pas possible";
            $this->addflash('success', $message);
        }
        else{
            $sortie->removeParticipant($this->getUser());
            $entityManager->persist($sortie);
            $entityManager->flush();

            // si le nombre max de participant n'est pas atteint, on met la sortie Ouverte
            $sortie = $sortieRepository->find($id);
            if ($sortie->getNbInscriptionsMax()>$sortie->getParticipants()->count()){
                $sortie->setEtat($etatOuverte);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

        }
        return $this->redirectToRoute('sorties_list');
    }

}

