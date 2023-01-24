<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"mail"}, message="Ce mail est déjà utilisé")
 * @UniqueEntity(fields={"pseudonyme"}, message="Ce pseudo est déjà utilisé")
 *
 */
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="veuillez renseigner un pseudonyme (entre 3 et 50 caractères)")
     * @Assert\Length(min=3,max=50,minMessage="Votre pseudonyme doit comporter 3 caractères minimum",maxMessage="Le pseudonyme doit pas dépasser 50 caractères")
     */
    private $pseudonyme;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner un nom")
     * @Assert\Length(min=2,max=255,minMessage="Votre nom doit comporter 2 caractères minimum",maxMessage="Le nom doit pas dépasser 255 caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner un prénom")
     * @Assert\Length(min=2,max=255,minMessage="Votre prénom doit comporter 2 caractères minimum",maxMessage="Le prénom doit pas dépasser 255 caractères")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=10,max=10,minMessage="Le numéro de téléphone doit comporter 10 caractères",maxMessage="Le numéro de téléphone doit comporter 10 caractères")
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message="Ce mail n'est pas valide")
     */
    private $mail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, inversedBy="participants")
     */
    private $sorties;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="participantOrganisateur", orphanRemoval=true)
     */
    private $sortiesPourUnOrganisateur;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->sortiesPourUnOrganisateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudonyme(): ?string
    {
        return $this->pseudonyme;
    }

    public function setPseudonyme(string $pseudonyme): self
    {
        $this->pseudonyme = $pseudonyme;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudonyme;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->pseudonyme;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties[] = $sorty;
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        $this->sorties->removeElement($sorty);

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesPourUnOrganisateur(): Collection
    {
        return $this->sortiesPourUnOrganisateur;
    }

    public function addSortiesPourUnOrganisateur(Sortie $sortiesPourUnOrganisateur): self
    {
        if (!$this->sortiesPourUnOrganisateur->contains($sortiesPourUnOrganisateur)) {
            $this->sortiesPourUnOrganisateur[] = $sortiesPourUnOrganisateur;
            $sortiesPourUnOrganisateur->setParticipantOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesPourUnOrganisateur(Sortie $sortiesPourUnOrganisateur): self
    {
        if ($this->sortiesPourUnOrganisateur->removeElement($sortiesPourUnOrganisateur)) {
            // set the owning side to null (unless already changed)
            if ($sortiesPourUnOrganisateur->getParticipantOrganisateur() === $this) {
                $sortiesPourUnOrganisateur->setParticipantOrganisateur(null);
            }
        }

        return $this;
    }
}
