<?php

namespace App\Entity; // Namespace de l'entité User

use App\Repository\UserRepository; // Repository lié à l'entité User
use Doctrine\Common\Collections\ArrayCollection; // Collection Doctrine pour gérer les relations OneToMany
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types; // Types Doctrine (ex: DATE_MUTABLE)
use Doctrine\ORM\Mapping as ORM; // Annotations ORM
use Symfony\Component\Security\Core\User\UserInterface; // Interface pour les utilisateurs Symfony
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface; // Interface pour gestion mot de passe
use Symfony\Component\Validator\Constraints as Assert; // Contraintes de validation Symfony
use ApiPlatform\Metadata\ApiResource; // Annotation API Platform pour exposer l'entité
use Symfony\Component\Serializer\Annotation\MaxDepth; // Limitation profondeur sérialisation (éviter récursion)
use Symfony\Component\Serializer\Annotation\Groups; // Groupes de sérialisation pour API Platform
use ApiPlatform\Metadata\Get; // Opérations API Platform (GET)
use ApiPlatform\Metadata\GetCollection; // Opérations API Platform (GET collection)

#[ORM\Entity(repositoryClass: UserRepository::class)] // Déclare l'entité avec son repository
#[ORM\HasLifecycleCallbacks] // Permet d'utiliser les callbacks (PrePersist, PreUpdate...)
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')", // Seulement les admins peuvent récupérer la liste des utilisateurs
            normalizationContext: ['groups' => ['user:read']] // Groupe de sérialisation pour cette opération
        ),
        new Get(
            security: "is_granted('PUBLIC_ACCESS')",  // Accès public possible pour une ressource unique
            normalizationContext: ['groups' => ['user:read:public']]  // Groupe de sérialisation public (plus restreint)
        ),
        // ... autres opérations possibles
    ],
    normalizationContext: ['groups' => ['user:read']], // Groupe par défaut à la lecture
    denormalizationContext: ['groups' => ['user:write']] // Groupe par défaut à l'écriture
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id] // Clé primaire
    #[ORM\GeneratedValue] // Auto-incrémentée
    #[ORM\Column] // Colonne standard
    #[Groups(['user:read', 'user:read:public'])]  // Inclue cet attribut dans ces groupes de sérialisation
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)] // Colonne string unique pour pseudo
    #[Assert\NotBlank] // Validation : ne doit pas être vide
    #[Assert\Length(min: 3, max: 255)] // Validation longueur minimale et maximale
    #[Groups(['user:read', 'user:read:public'])]  // Exposé dans groupes lecture publics et privés
    private ?string $pseudo = null;

    #[ORM\Column(length: 255, unique: true)] // Colonne string unique pour email
    #[Assert\NotBlank] // Validation obligatoire
    #[Assert\Email] // Validation format email
    private ?string $email = null;

    #[ORM\Column(length: 255)] // Colonne pour le hash du mot de passe
    #[Assert\NotBlank(groups: ['user:write'])] // Validation obligatoire seulement à l'écriture
    #[Assert\Length(min: 8, groups: ['user:write'])] // Minimum 8 caractères pour mot de passe à l'écriture
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)] // Colonne nullable pour URL d'image profil
    #[Assert\Url] // Validation format URL
    private ?string $imgUrl = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)] // Date de naissance nullable
    #[Assert\Date] // Validation format date
    private ?\DateTime $birthday = null;

    #[ORM\Column] // Date de création non nullable (immutable)
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)] // Date de mise à jour nullable
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::JSON)] // Roles stockés en JSON (tableau de chaînes)
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Act::class, orphanRemoval: true)] // Relation OneToMany avec Act
    #[MaxDepth(1)] // Limite profondeur sérialisation
    private Collection $acts;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)] // Relation OneToMany avec Comment
    #[MaxDepth(1)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Like::class, orphanRemoval: true)] // Relation OneToMany avec Like
    #[MaxDepth(1)]
    private Collection $likes;

    public function __construct()
    {
        $this->roles = ['ROLE_USER']; // Rôle par défaut
        $this->acts = new ArrayCollection(); // Initialise collection Acts
        $this->comments = new ArrayCollection(); // Initialise collection Comments
        $this->likes = new ArrayCollection(); // Initialise collection Likes
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable(); // Initialise createdAt à la date courante si null
        }
    }

    // Getter id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pseudo
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    // Setter pseudo
    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    // Getter email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Setter email
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Retourne le mot de passe hashé (interface PasswordAuthenticatedUserInterface)
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    // Setter mot de passe (hashé)
    public function setPassword(string $password): static
    {
        $this->password = $password; 
        return $this;
    }

    // Getter URL image profil
    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    // Setter URL image profil
    public function setImgUrl(?string $imgUrl): static
    {
        $this->imgUrl = $imgUrl;
        return $this;
    }

    // Getter date anniversaire
    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    // Setter date anniversaire
    public function setBirthday(?\DateTime $birthday): static
    {
        $this->birthday = $birthday;
        return $this;
    }

    // Getter date création
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Getter date mise à jour
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Retourne la liste des rôles (avec ROLE_USER assuré)
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Assure que ROLE_USER est toujours présent
        return array_unique($roles);
    }

    // Setter roles
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    // Getter collection Acts
    public function getActs(): Collection
    {
        return $this->acts;
    }

    // Ajoute un Act à la collection et lie l'utilisateur
    public function addAct(Act $act): static
    {
        if (!$this->acts->contains($act)) {
            $this->acts->add($act);
            $act->setUser($this);
        }
        return $this;
    }

    // Supprime un Act de la collection et dissocie l'utilisateur
    public function removeAct(Act $act): static
    {
        if ($this->acts->removeElement($act)) {
            if ($act->getUser() === $this) {
                $act->setUser(null);
            }
        }
        return $this;
    }

    // Getter collection Comments
    public function getComments(): Collection
    {
        return $this->comments;
    }

    // Ajoute un Comment et lie l'utilisateur
    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }
        return $this;
    }

    // Supprime un Comment et dissocie l'utilisateur
    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }
        return $this;
    }

    // Getter collection Likes
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    // Ajoute un Like et lie l'utilisateur
    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUser($this);
        }
        return $this;
    }

    // Supprime un Like et dissocie l'utilisateur
    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }
        return $this;
    }

    /**
     * Retourne l'identifiant unique de l'utilisateur (email ici)
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * Efface les données sensibles temporaires (ex: mot de passe en clair)
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Ici on pourrait effacer des données sensibles temporaires si besoin
    }

    /**
     * Setter date création (utile dans certains cas)
     * 
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Setter date mise à jour
     * 
     * @return  self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Méthode magique pour représenter l'objet en string (affiche pseudo, email ou id)
    public function __toString(): string
    {
        return $this->getPseudo() ?? $this->getEmail() ?? (string)$this->getId();
    }
}
