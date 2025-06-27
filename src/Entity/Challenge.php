<?php

namespace App\Entity; // Namespace de l'entité Challenge

// Import des classes nécessaires
use App\Repository\ChallengeRepository;
use Doctrine\Common\Collections\ArrayCollection; // Pour gérer les collections (relations OneToMany)
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types; // Pour les types de colonnes Doctrine
use Doctrine\ORM\Mapping as ORM; // Annotations ORM
use Symfony\Component\Validator\Constraints as Assert; // Contraintes de validation
use ApiPlatform\Metadata\ApiResource; // API Platform pour exposer l'entité en API
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Serializer\Annotation\MaxDepth; // Pour limiter la profondeur de sérialisation

#[ORM\Entity(repositoryClass: ChallengeRepository::class)] // Définit l'entité avec son repository
#[ORM\HasLifecycleCallbacks] // Permet d'utiliser des callbacks sur les événements du cycle de vie (ex: prePersist)
#[ApiResource( // Configuration des opérations API
    operations: [
        new GetCollection(security: "is_granted('PUBLIC_ACCESS')"),  // Accès public pour récupérer la liste
        new Get(security: "is_granted('PUBLIC_ACCESS')"),           // Accès public pour récupérer un seul élément
        new Post(security: "is_granted('IS_AUTHENTICATED_FULLY')"), // Création réservée aux utilisateurs authentifiés
        new Put(security: "is_granted('IS_AUTHENTICATED_FULLY')"),  // Modification réservée aux utilisateurs authentifiés
    ]
)]
class Challenge
{
    #[ORM\Id] // Clé primaire
    #[ORM\GeneratedValue] // Auto-incrémentée
    #[ORM\Column] // Colonne standard (type int par défaut)
    private ?int $id = null;

    #[ORM\Column(length: 255)] // Colonne string max 255 caractères
    #[Assert\NotBlank] // Validation : champ obligatoire
    #[Assert\Length(min: 3, max: 255)] // Validation : longueur entre 3 et 255 caractères
    private ?string $title = null;

    #[ORM\Column(type: Types::INTEGER)] // Colonne int
    #[Assert\NotBlank] // Champ obligatoire
    #[Assert\Positive] // Doit être strictement positif (> 0)
    private ?int $objective = null; // Objectif numérique du challenge

    #[ORM\Column(type: Types::INTEGER)] // Colonne int
    #[Assert\NotBlank] // Champ obligatoire
    #[Assert\PositiveOrZero] // Doit être positif ou zéro
    private ?int $progress = null; // Progression actuelle

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)] // Date de création (immutable)
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true, type: Types::DATETIME_IMMUTABLE)] // Date de mise à jour nullable
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'challenge', targetEntity: Act::class, orphanRemoval: true)]
    #[MaxDepth(1)] // Limite la profondeur de sérialisation pour éviter récursion infinie
    private Collection $acts; // Collection des actes liés à ce challenge

    public function __construct()
    {
        $this->acts = new ArrayCollection(); // Initialise la collection vide
        $this->createdAt = new \DateTimeImmutable(); // Initialise la date de création à maintenant
    }

    // Getter de l'identifiant
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et setter du titre
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    // Getter et setter de l'objectif
    public function getObjective(): ?int
    {
        return $this->objective;
    }

    public function setObjective(int $objective): static
    {
        $this->objective = $objective;
        return $this;
    }

    // Getter et setter de la progression
    public function getProgress(): ?int
    {
        return $this->progress;
    }

    public function setProgress(int $progress): static
    {
        $this->progress = $progress;
        return $this;
    }

    // Getter de la date de création
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Getter de la date de mise à jour
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Retourne la collection d'actes liés à ce challenge
     * 
     * @return Collection<int, Act>
     */
    public function getActs(): Collection
    {
        return $this->acts;
    }

    // Ajoute un acte à la collection et lie le challenge à cet acte
    public function addAct(Act $act): static
    {
        if (!$this->acts->contains($act)) {
            $this->acts->add($act);
            $act->setChallenge($this);
        }

        return $this;
    }

    // Retire un acte de la collection et dissocie le challenge de l'acte
    public function removeAct(Act $act): static
    {
        if ($this->acts->removeElement($act)) {
            if ($act->getChallenge() === $this) {
                $act->setChallenge(null);
            }
        }

        return $this;
    }

    /**
     * Setter de la date de création (utile pour les callbacks ou initialisation manuelle)
     *
     * @return  self
     */ 
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Setter de la date de mise à jour (utile pour les callbacks ou mise à jour manuelle)
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    // Méthode magique permettant d'afficher l'entité sous forme de chaîne (ex : dans les formulaires)
    public function __toString(): string
    {
        // Retourne le titre si défini, sinon l'id converti en string
        return $this->getTitle() ?? (string)$this->getId();
    }
}
