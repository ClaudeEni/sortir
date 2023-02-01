<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CreerParticipantType;
use App\Form\ImportParticipantType;
use App\Form\ModificationProfilMDPType;
use App\Form\ModificationProfilType;
use App\Repository\CampusRepository;
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
                                  EntityManagerInterface $entityManager,
                                  ModificationProfilType $modificationProfilType,
                                  ParticipantRepository $participantRepository,
                                  CampusRepository $campusRepository) : Response {
        $participant = new Participant();

        // création du formulaire pour saisie des informations
        $nouveauProfil = $this->createForm(CreerParticipantType::class, $participant);
        $nouveauProfil->handleRequest($request);

        $importParticipantForm = $this->createForm(ImportParticipantType::class);
        $importParticipantForm->handleRequest($request);


        if ($importParticipantForm->get('importer')->isClicked()) {

            $file = $importParticipantForm->get('import')->getData();

            // Open the file
            if (($handle = fopen($file->getPathname(), "r")) !== false) {
                $user = 0;
                $userko = 0;
                while (($data = fgetcsv($handle,1024,';')) !== false) {

                    // recherche du campus
                    $campus = $campusRepository->findOneBy(["nom"=>$data[0]]);
                    // un participant est-il déjà créé avec ce mail ou ce pseudo
                    $participant_mail = $participantRepository->findOneBy(["mail"=>$data[4]]);
                    $participant_pseudo = $participantRepository->findOneBy(["pseudonyme"=>$data[5]]);

                    // si un participant existe avec le même mail ou pseudo ou que le campus n'existe pas, on ne l'ajoute pas
                    if ($participant_mail or $participant_pseudo or !$campus){
                        $userko++;
                    }
                    else{
                        $participant = new Participant();

                        $participant->setCampus($campus);
                        $participant->setNom($data[1]);
                        $participant->setPrenom($data[2]);
                        $participant->setTelephone($data[3]);
                        $participant->setMail($data[4]);
                        $participant->setPseudonyme($data[5]);
                        $participant->setPassword($passwordHasher->hashPassword($participant,$data[6]));
                        $participant->setAdministrateur(false);
                        $participant->setActif(true);
                        $entityManager->persist($participant);
                        $entityManager->flush();
                        $user++;
                    }
                }
                if($user>0){
                    $message= ($user===1) ? " utilisateur a été créé avec succès" : " utilisateurs ont été créés avec succès";
                    $this->addflash('success', $user.$message);
                }
                if($userko>0){
                    $messageko=($userko===1) ? " utilisateur " : " utilisateurs.";
                    $this->addflash('warning',  "Erreur sur ".$userko.$messageko." Le mail et/ou le pseudo existe déjà ou le campus n'existe pas!");
                }
                fclose($handle);
            }

            return $this->render('profil/CreerProfil.html.twig', [
                "participant" => $participant,
                "participantForm" => $nouveauProfil->createView(),
                "participantImportForm"=>$importParticipantForm->createView()
            ]);
        }

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
            "participantForm" => $nouveauProfil->createView(),
            "participantImportForm"=>$importParticipantForm->createView()
        ]);
    }

    /**
     * @Route ("profil/import",name="profil_import")
     */
    public function uploadUser(Request $request) : Response{

        $participant = new Participant();

        // création du formulaire pour saisie des informations
        $nouveauProfil = $this->createForm(CreerParticipantType::class, $participant);
        $nouveauProfil->handleRequest($request);

        echo('Début de l\'import');
        $file = $nouveauProfil->get('import')->getData();
        if ($file) {
            echo("fichier sélectionné");
            // Open the file
            if (($handle = fopen($file->getPathname(), "r")) !== false) {
                echo("fichier : ".$file->getPathname());
                // Read and process the lines.
                // Skip the first line if the file includes a header
//                while (($data = fgetcsv($handle)) !== false) {
//                    // Do the processing: Map line to entity, validate if needed
//                    $entity = new Entity();
//                    // Assign fields
//                    $entity->setField1($data[0]);
//                    $em->persist($entity);
//                }
//                fclose($handle);
//                $em->flush();
            }
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

