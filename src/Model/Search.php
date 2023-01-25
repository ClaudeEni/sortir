<?php

namespace App\Model;

use App\Entity\Campus;
use DateTimeInterface;
use Symfony\Config\TwigExtra\StringConfig;

class Search
{
    private ?String $nom = null;

    private $sortiePassee = false;

    private ?Campus $campus = null;

    private ?DateTimeInterface $dateDebut = null;

    private ?DateTimeInterface $dateFin = null;

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

    public function __toString() : String
    {
        return "";
    }

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
}