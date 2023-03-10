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
 * @UniqueEntity(fields={"pseudo"},message="ce pseudo existe deja")
 * @UniqueEntity(fields={"mail"},message="ce mail existe deja")
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
     * @Assert\Email(message="veuillez saisir votre email")
     */
    private $mail;



    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Veuillez saisir votre nom")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Veuillez saisir votre prénom")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=50,unique=true)
     * @Assert\NotBlank(message="Veuillez saisir un pseudo")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un mot de passe")
     */
    private $motPasse;
    /**
     * @var string le mot de passe en clair pour gestion formulaire
     */
    private $plainMotPasse;

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
    private $inscriptionsSorties;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $sortiesCreees;

    public function __construct()
    {
        $this->inscriptionsSorties = new ArrayCollection();
        $this->sortiesCreees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->mail;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->mail;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->administrateur ?['ROLE_ADMIN']:['ROLE_USER'];
    }



    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->motPasse;
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

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getMotPasse(): ?string
    {
        return $this->motPasse;
    }

    public function setMotPasse(string $motPasse): self
    {
        $this->motPasse = $motPasse;

        return $this;
    }
    public function getPlainMotPasse(): ?string
    {
        return $this->motPasse;
    }

    public function setPlainMotPasse(string $motPasse): self
    {
        $this->plainMotPasse = $motPasse;

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
    public function getInscriptionsSorties(): Collection
    {
        return $this->inscriptionsSorties;
    }

    public function addInscriptionsSorty(Sortie $inscriptionsSorty): self
    {
        if (!$this->inscriptionsSorties->contains($inscriptionsSorty)) {
            $this->inscriptionsSorties[] = $inscriptionsSorty;
        }

        return $this;
    }

    public function removeInscriptionsSorty(Sortie $inscriptionsSorty): self
    {
        $this->inscriptionsSorties->removeElement($inscriptionsSorty);

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesCreees(): Collection
    {
        return $this->sortiesCreees;
    }

    public function addSortiesCreee(Sortie $sortiesCreee): self
    {
        if (!$this->sortiesCreees->contains($sortiesCreee)) {
            $this->sortiesCreees[] = $sortiesCreee;
            $sortiesCreee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesCreee(Sortie $sortiesCreee): self
    {
        if ($this->sortiesCreees->removeElement($sortiesCreee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesCreee->getOrganisateur() === $this) {
                $sortiesCreee->setOrganisateur(null);
            }
        }
        return $this;
    }
}
