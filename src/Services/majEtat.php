<?php

namespace App\Services;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class majEtat
{

    public function maj(EntityManagerInterface $entityManager,
                        SortieRepository $sortieRepository,
                        EtatRepository $etatRepository){

        $sorties = $sortieRepository->findAll();
        $etatOuverte = $etatRepository->findOneBy(['libelle'=>'Ouverte']);
        $etatCloture = $etatRepository->findOneBy(['libelle'=>'Clôturée']);
        $enCours     = $etatRepository->findOneBy(['libelle'=>'En cours']);
        $etatAnnule  = $etatRepository->findOneBy(['libelle'=>'Annulée']);
        $etatPassee  = $etatRepository->findOneBy(['libelle'=>'Passée']);

        // parcours de toutes les sorties
        foreach ($sorties as $sortie){

            // sortie ouverte avec date limite d'inscription dépassée = CLOTURE
            if ($sortie->getEtat()===$etatOuverte and $sortie->getDateLimiteInscription()<new \DateTime('now')){
                $sortie->setEtat($etatCloture);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }
            // sortie cloturée et aucun participant = ANNULEE
            if ($sortie->getEtat()===$etatCloture and $sortie->getParticipants()->count()==0){
                $sortie->setEtat($etatAnnule);
                $sortie->setInfosSortie('Aucun participant à la date limite, la sortie est annulée');
                $entityManager->persist($sortie);
                $entityManager->flush();
            }
            // sortie cloturée avec participant et dans le temps de la sortie = EN COURS
            if ($sortie->getEtat()===$etatCloture and $sortie->getParticipants()->count()>0 and
                   $sortie->getDateHeureDebut()<new \DateTime('now') and
                   $sortie->getDateHeureDebut()->format('Y/m/d H:h')>date('Y/m/d H:i', strtotime('-'.$sortie->getDuree().' minutes'))){
                //echo(date('Y/m/d H:i', strtotime('-'.$sortie->getDuree().' minutes')));
                $sortie->setEtat($enCours);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

            // sortie avec état en cours qui est terminée = PASSEE
            if ($sortie->getEtat()===$enCours and $sortie->getDateHeureDebut()->format('Y/m/d H:h')<date('Y/m/d H:i', strtotime('-'.$sortie->getDuree().' minutes'))){
                $sortie->setEtat($etatPassee);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

            // toutes les sorties de plus de 30 jours = PASSEE
//            if ($sortie->getDateHeureDebut()->format('Y/m/d')< date('Y/m/d', strtotime('-30 days'))){
//                $sortie->setEtat($etatPassee);
//                $entityManager->persist($sortie);
//                $entityManager->flush();
//            }

        }

    }

}