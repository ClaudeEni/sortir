<?php

namespace App\Controller;

use App\Form\ModificationProfilMDPType;
use App\Form\ModificationProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    /**
     * @Route("/profil/{id}", name="profil_afficher")
     */
    public function afficherProfil($id,
                                   Request $request,
                                   ParticipantRepository $participantRepository,
                                   EntityManagerInterface $entityManager): Response
    {
        $participant = $participantRepository->find($id);

        if (!$participant){
            throw $this->createNotFoundException('Ce participant n\'existe pas !');
        }

        // création du formulaire pour modification
        $modificationProfilForm = $this->createForm(ModificationProfilType::class,$participant);
        $modificationProfilForm->handleRequest($request);

        if($modificationProfilForm->isSubmitted() && $modificationProfilForm->isValid()){
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('home',[]);
        }

        // si le participant est celui connecté, on affiche son profil pour modification
        // sinon on affiche le profil en consultation
        return $this->render('profil/profil.html.twig', [
            "participant"=>$participant,
            "ParticipantForm"=>$modificationProfilForm->createView()
        ]);
    }

    /**
     * @Route("/profil/modifmdp/{id}",name="modif_mdp")
     */
    public function modifMDP($id,
                            Request $request,
                            EntityManagerInterface $entityManager,
                            ParticipantRepository $participantRepository,
                            UserPasswordHasherInterface $passwordHasher) : Response
    {
        $participant = $participantRepository->find($id);

        if (!$participant){
            throw $this->createNotFoundException('Ce participant n\'existe pas !');
        }

        // création du formulaire pour modification du mot de passe
        $modificationMdpForm = $this->createForm(ModificationProfilMDPType::class,$participant);
        $modificationMdpForm->handleRequest($request);

        if($modificationMdpForm->isSubmitted() && $modificationMdpForm->isValid()){
            $participant->setPassword(
                $passwordHasher->hashPassword($participant,$modificationMdpForm->get('password')->getData())
            );
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('home',[]);
        }

        return $this->render('profil/ModifMdp.html.twig', [
            "participant"=>$participant,
            "ParticipantForm"=>$modificationMdpForm->createView()
        ]);
    }
}

