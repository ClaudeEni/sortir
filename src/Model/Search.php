<?php

namespace App\Model;

use App\Entity\Campus;
use DateTimeInterface;

class Search
{
    private ?String $nom = null;

    private ?Campus $campus = null;

    private ?DateTimeInterface $dateDebut = null;

    private ?DateTimeInterface $dateFin = null;

    private $sortiePassee = false;

    private $sortieOrganisateur = false;

    private $sortieInscrit = false;

    private $sortiePasInscrit = false;



    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

//    public function __toString() : String
//    {
//        return "";
//    }

    /**
     * @return bool
     */
    public function isSortiePassee(): bool
    {
        return $this->sortiePassee;
    }

    /**
     * @param bool $sortiePassee
     */
    public function setSortiePassee(bool $sortiePassee): void
    {
        $this->sortiePassee = $sortiePassee;
    }


    /**
     * @return Campus
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param int $campus
     */
    public function setCampus(Campus $campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    /**
     * @param DateTimeInterface|null $dateDebut
     */
    public function setDateDebut(?DateTimeInterface $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateFin(): ?DateTimeInterface
    {
        return $this->dateFin;
    }

    /**
     * @param DateTimeInterface|null $dateFin
     */
    public function setDateFin(?DateTimeInterface $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    /**
     * @return bool
     */
    public function isSortieOrganisateur(): bool
    {
        return $this->sortieOrganisateur;
    }

    /**
     * @param bool $sortieOrganisateur
     */
    public function setSortieOrganisateur(bool $sortieOrganisateur): void
    {
        $this->sortieOrganisateur = $sortieOrganisateur;
    }

    /**
     * @return bool
     */
    public function isSortieInscrit(): bool
    {
        return $this->sortieInscrit;
    }

    /**
     * @param bool $sortieInscrit
     */
    public function setSortieInscrit(bool $sortieInscrit): void
    {
        $this->sortieInscrit = $sortieInscrit;
    }

    /**
     * @return bool
     */
    public function isSortiePasInscrit(): bool
    {
        return $this->sortiePasInscrit;
    }

    /**
     * @param bool $sortiePasInscrit
     */
    public function setSortiePasInscrit(bool $sortiePasInscrit): void
    {
        $this->sortiePasInscrit = $sortiePasInscrit;
    }
}