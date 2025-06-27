<?php

namespace App\Entity; // Namespace de l'entité Comment

// Import des classes nécessaires
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // Validation des données
use ApiPlatform\Metadata\ApiResource; // Pour exposer l'entité en API
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter; // Filtre de recherche ApiPlatform
use Symfony\Component\Serializer\Annotation\MaxDepth; // Limite la profondeur de sérialisation
use Symfony\Component\Serializer\Annotation\Groups; // Gestion des groupes de sérialisation
use Symfony\Bundle\SecurityBundle\Security;

#[ORM\Entity(repositoryClass: CommentRepository::class)] // Entité Doctrine liée au repository CommentRepository
#[ORM\HasLifecycleCallbacks] // Permet d’utiliser les callbacks sur les événements du cycle de vie
#[ApiResource( // Configuration des opérations API pour cette entité
    operations: [
        new GetCollection(
            security: "is_granted('PUBLIC_ACCESS')", // Accès public pour récupérer la collection de commentaires
            normalizationContext: ['groups' => ['comment:read']] // Groupe de sérialisation pour la lecture
        ),
        new Post(
            security: "is_granted('ROLE_USER')", // Seuls les utilisateurs connectés peuvent créer un commentaire
            denormalizationContext: ['groups' => ['comment:write']] // Groupe de sérialisation pour l'écriture
        ),
        new Delete(
            security: "is_granted('ROLE_USER') and object.getUser() == user" // Seuls le propriétaire du commentaire peut le supprimer
        )
    ],
    normalizationContext: ['groups' => ['comment:read']], // Groupe de lecture par défaut
    denormalizationContext: ['groups' => ['comment:write']] // Groupe d’écriture par défaut
)]
#[ApiFilter(SearchFilter::class, properties: ['act' => 'exact'])] // Filtre par act (exact)
class Comment
{
    #[ORM\Id] // Clé primaire
    #[ORM\GeneratedValue] // Auto-incrémentée
    #[ORM\Column]
    #[Groups(['comment:read'])] // Visible en lecture uniquement
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)] // Colonne texte pour le contenu
    #[Assert\NotBlank] // Champ obligatoire
    #[Assert\Length(min: 2, max: 1000)] // Longueur entre 2 et 1000 caractères
    #[Groups(['comment:read', 'comment:write'])] // Visible en lecture et écriture
    private ?string $content = null;

    #[ORM\Column] // Colonne date de création
    #[Groups(['comment:read'])] // Visible en lecture uniquement
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)] // Colonne date de mise à jour (nullable)
    #[Groups(['comment:read'])] // Visible en lecture uniquement
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')] // Relation ManyToOne avec l'entité Act
    #[ORM\JoinColumn(nullable: false)] // Clé étrangère obligatoire
    #[Assert\NotNull] // Champ obligatoire
    #[MaxDepth(1)] // Limite la profondeur de sérialisation pour éviter récursion infinie
    #[Groups(['comment:read', 'comment:write'])] // Visible en lecture et écriture
    private ?Act $act = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')] // Relation ManyToOne avec l'entité User (auteur)
    #[Groups(['comment:read', 'comment:write'])] // Visible en lecture et écriture
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(); // Initialise la date de création à maintenant
    }

    // Getter de l'identifiant
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et setter du contenu
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
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

    // Getter et setter de l'entité Act liée
    public function getAct(): ?Act
    {
        return $this->act;
    }

    public function setAct(?Act $act): static
    {
        $this->act = $act;
        return $this;
    }

    // Getter et setter de l'utilisateur auteur
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Setter manuel de la date de création (utile pour des cas spécifiques ou tests)
     *
     * @return  self
     */ 
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Setter manuel de la date de mise à jour (utile pour des cas spécifiques ou tests)
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PrePersist] // Callback appelé avant l'insertion en base
    public function onPrePersist(): void
    {
        // Si la date de création n'est pas définie, on la fixe à maintenant
        if (!$this->createdAt) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    #[ORM\PreUpdate] // Callback appelé avant la mise à jour en base
    public function onPreUpdate(): void
    {
        // Met à jour la date de modification à maintenant
        $this->updatedAt = new \DateTimeImmutable();
    }
}
