<?php

namespace App\Entity; // Namespace de l'entité Contact

use App\Repository\ContactRepository; // Repository associé
use Doctrine\DBAL\Types\Types; // Types Doctrine pour les colonnes
use Doctrine\ORM\Mapping as ORM; // Annotations Doctrine ORM
use Symfony\Component\Validator\Constraints as Assert; // Contraintes de validation
use ApiPlatform\Metadata\ApiResource; // Ressource API Platform
use ApiPlatform\Metadata\Post; // Opération POST pour API Platform

#[ORM\Entity(repositoryClass: ContactRepository::class)] // Entité Doctrine liée au ContactRepository
#[ORM\HasLifecycleCallbacks] // Permet d’utiliser des callbacks sur les événements du cycle de vie (ex : PrePersist)
#[ApiResource( // Configuration API Platform pour exposer l'entité en API REST
    operations: [
        new Post(
            uriTemplate: '/contacts', // URL spécifique pour la création de contacts
            security: "is_granted('PUBLIC_ACCESS')" // Accès public pour créer un contact (pas besoin d'être authentifié)
        )
    ]
)]
class Contact
{
    #[ORM\Id] // Clé primaire
    #[ORM\GeneratedValue] // Auto-incrémentée
    #[ORM\Column] // Colonne standard (type int)
    private ?int $id = null;

    #[ORM\Column(length: 100)] // Colonne string avec longueur max 100
    #[Assert\NotBlank] // Champ obligatoire
    #[Assert\Length(min: 2, max: 100)] // Doit faire entre 2 et 100 caractères
    private ?string $firstName = null; // Prénom du contact

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    private ?string $name = null; // Nom de famille du contact

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email] // Doit être une adresse email valide
    private ?string $email = null; // Email du contact

    #[ORM\Column(length: 50)]
    #[Assert\NotNull] // Champ obligatoire (peut être vide mais pas null)
    private ?string $subject = null; // Sujet du message/contact

    #[ORM\Column(type: Types::TEXT)] // Colonne texte pour le message complet
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 2000)] // Longueur entre 10 et 2000 caractères
    private ?string $message = null; // Message du contact

    #[ORM\Column] // Date et heure de création du contact
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)] // Date de mise à jour (nullable)
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(); // Initialise createdAt à la date et heure actuelle
    }

    // Getter de l'identifiant
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et setter du prénom
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    // Getter et setter du nom
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    // Getter et setter de l'email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Getter et setter du message
    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    // Getter de la date de création
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Getter et setter de la date de mise à jour
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Setter manuel de la date de création (peut servir pour certains cas particuliers)
     *
     * @return  self
     */ 
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Setter du sujet du message
     *
     * @return  self
     */ 
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Getter du sujet du message
     */ 
    public function getSubject()
    {
        return $this->subject;
    }
}
