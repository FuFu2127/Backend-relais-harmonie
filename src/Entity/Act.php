<?php

namespace App\Entity; // Déclaration du namespace où se trouve cette classe

use App\Entity\Like; // Import des classes utilisées dans cette entité
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich; // Annotation pour la gestion des fichiers uploadés
use ApiPlatform\Metadata\ApiProperty;

use Doctrine\DBAL\Types\Types; // Import des types de Doctrine pour les colonnes
use Doctrine\ORM\Mapping as ORM; // Import des annotations ORM pour la gestion de la base de données
use App\Repository\ActRepository; // Import du repository pour cette entité
use ApiPlatform\Metadata\ApiResource; // Import de l'API Platform pour la gestion des ressources API
use Doctrine\Common\Collections\Collection; // Import de la collection de Doctrine pour gérer les relations
use Doctrine\Common\Collections\ArrayCollection; // Import concrete de la collection de Doctrine
use Symfony\Component\Serializer\Annotation\MaxDepth; // Annotation pour la gestion de la profondeur de sérialisation
use Symfony\Component\Validator\Constraints as Assert; // Import des contraintes de validation
use App\DataTransformer\MultipartActDataTransformer; // Import du DataTransformer pour gérer la logique de création d'actes

#[ORM\Entity(repositoryClass: ActRepository::class)] // Déclaration de l'entité Act avec son repository
#[ORM\HasLifecycleCallbacks] // Indique que cette entité a des callbacks de cycle de vie
#[ApiResource(
    operations: [
        new \ApiPlatform\Metadata\Get(), // Opération GET pour lire un seul élément (publique)
        new \ApiPlatform\Metadata\Post( // Opération POST pour créer un nouvel acte
            security: "is_granted('IS_AUTHENTICATED_FULLY')", // Authentification requise pour créer
            inputFormats: ['multipart' => ['multipart/form-data']], // Format attendu
            deserialize: false, // Désérialisation gérée par le DataTransformer
            processor: MultipartActDataTransformer::class // Utilisation du DataTransformer pour gérer la logique de création
        ),
        new \ApiPlatform\Metadata\GetCollection(), // Accès public pour la collection
    ]
)]
#[ORM\EntityListeners(['App\EventListener\ActListener'])] // Écouteur d'événements pour gérer les actions sur l'entité Act
/**
 * @Vich\Uploadable
 */
class Act
{
    #[ORM\Id] // Déclaration de la clé primaire
    #[ORM\GeneratedValue] // Auto-incrémentation de la clé primaire
    #[ORM\Column] // Colonne dans la table (par défaut de type integer)
    private ?int $id = null;

    #[ORM\Column(length: 255)] // Colonne string max 255 caractères
    #[Assert\NotBlank] // Validation pour s'assurer que le champ n'est pas vide
    #[Assert\Length(min: 3, max: 255)] // Validation : longueur entre 3 et 255 caractères
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)] // Colonne de type string 
    #[Assert\NotBlank] // Validation pour s'assurer que le champ n'est pas vide
    #[Assert\Length(min: 10, max: 1000)] // Validation : longueur entre 10 et 1000 caractères
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)] // Colonne string optionnelle pour l'URL de l'image
    #[Assert\Url] // Validation pour s'assurer que c'est une URL valide
    private ?string $imgUrl = null;

    #[ORM\Column(length: 255)] // Colonne sting pour la catégorie de l'acte
    #[Assert\NotBlank] // Validation pour s'assurer que le champ n'est pas vide
    #[Assert\Length(min: 3, max: 255)] // Validation : longueur entre 3 et 255 caractères
    private ?string $category = null;

    #[ORM\Column] 
    private ?\DateTimeImmutable $createdAt = null; // Colonne pour la date de création de l'acte

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null; // Colonne pour la date de mise à jour de l'acte

    #[ORM\ManyToOne(inversedBy: 'acts')] // Relation ManyToOne avec l'entité User inverse "acts"
    #[ORM\JoinColumn(nullable: true)] // La colonne ne peut être null 
    #[MaxDepth(1)] // Limite la profondeur de sérialisation pour éviter les boucles infinies
    private ?User $user = null; 

    #[ORM\ManyToOne(inversedBy: 'acts')] // Relation ManyToOne avec l'entité Challenge inverse "acts"
    #[MaxDepth(1)] // Limite la profondeur de sérialisation pour éviter les boucles infinies
    private ?Challenge $challenge = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])] // Relation OneToOne avec l'entité Location 
    #[MaxDepth(1)] // Limite la profondeur de sérialisation pour éviter les boucles infinies
    private ?Location $location = null;

    #[ORM\OneToMany(mappedBy: 'act', targetEntity: Comment::class, orphanRemoval: true)] // Relation OneToMany avec l'entité Comment, les commentaires orphelins sont supprimés
    #[MaxDepth(1)] // Limite la profondeur de sérialisation pour éviter les boucles infinies
    private Collection $comments;


    #[ORM\OneToMany(mappedBy: 'act', targetEntity: Like::class, orphanRemoval: true)] // Relation OneToMany avec l'entité Like, les likes orphelins sont supprimés
    #[MaxDepth(1)] // Limite la profondeur de sérialisation pour éviter les boucles infinies
    private Collection $likes;

    /**
     * @Vich\UploadableField(mapping="act_images", fileNameProperty="image")
     */
    #[ApiProperty(types: ['https://schema.org/image'], writable: true)]
    private ?File $imageFile = null; // Champ pour gérer le fichier image uploadé

    #[ORM\Column(type: 'string', length: 255, nullable: true)] // Colonne pour stocker le nom de l'image après upload
    private ?string $image = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(); // Initialisation de la date de création à l'heure actuelle
        $this->comments = new ArrayCollection(); // Initialisation de la collection de commentaires vide
        $this->likes = new ArrayCollection(); // Initialisation de la collection de likes vide
    }

    /**
     * Getters et Setters pour les propriétés de l'entité Act
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(?string $imgUrl): static
    {
        $this->imgUrl = $imgUrl;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getChallenge(): ?Challenge
    {
        return $this->challenge;
    }

    public function setChallenge(?Challenge $challenge): static
    {
        $this->challenge = $challenge;
        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static // Ajoute un commentaire en prenant soin de lier l'acte au commentaire
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAct($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): static // Retire un commentaire et dissocier l'acte du commentaire
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getAct() === $this) {
                $comment->setAct(null);
            }
        }
        return $this;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static // Ajoute un like en prenant soin de lier l'acte au like
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setAct($this);
        }
        return $this;
    }

    public function removeLike(Like $like): static // Retire un like et dissocier l'acte du like
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getAct() === $this) {
                $like->setAct(null);
            }
        }
        return $this;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        if ($imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function __toString(): string // Méthode magique pour convertir l'objet en chaîne de caractères
    {
        return $this->title ?? 'Acte';
    }
}