<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CreerParticipantType;
use App\Form\ModificationProfilMDPType;
use App\Form\ModificationProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilController extends AbstractController
{
//    /**
//     * @Route("/", name="home")
//     */
//    public function home(): Response
//    {
//        return $this->render('profil/index.html.twig', [
//            'controller_name' => 'ProfilController',
//        ]);
//    }

    /**
     * @Route("/profil/nouveau",name="nouveau_profil")
     */
    public function nouveauProfil(Request $request,
                                  UserPasswordHasherInterface $passwordHasher,
                                  EntityManagerInterface $entityManager) : Response {
        $participant = new Participant();
        dump($participant);

        // création du formulaire pour saisie des informations
        $nouveauProfil = $this->createForm(CreerParticipantType::class, $participant);
        $nouveauProfil->handleRequest($request);

        if ($nouveauProfil->isSubmitted() and $nouveauProfil->isValid()) {
            $participant->setActif(true);
            $participant->setPassword(
                $passwordHasher->hashPassword($participant, $nouveauProfil->get('password')->getData())
            );
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('sorties_list',[]);
        }

        return $this->render('profil/CreerProfil.html.twig', [
            "participant" => $participant,
            "participantForm" => $nouveauProfil->createView()
        ]);
    }


    /**
     * @Route("/profil/{id}", name="profil_afficher")
     */
    public function afficherProfil($id,
                                   Request $request,
                                   ParticipantRepository $participantRepository,
                                   SluggerInterface $slugger,
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
            // upload the avatar file
            $avatarFile = $modificationProfilForm->get('avatar')->getData();
            if ($avatarFile) {
                // Move the file to the directory where avatars are stored
                try {
                    $avatarFile->move(
                        $this->getParameter('avatar_directory'),
                        $participant->getId().'.'.$avatarFile->guessExtension()
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }

            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('sorties_list',[]);
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

            return $this->redirectToRoute('sorties_list',[]);
        }

        return $this->render('profil/ModifMdp.html.twig', [
            "participant"=>$participant,
            "ParticipantForm"=>$modificationMdpForm->createView()
        ]);
    }


}

