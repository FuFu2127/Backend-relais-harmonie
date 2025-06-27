<?php

namespace App\Entity; // Namespace de l'entité

use App\Repository\LocationRepository; // Repository lié à l'entité Location
use Doctrine\DBAL\Types\Types; // Types Doctrine (pour les colonnes)
use Doctrine\ORM\Mapping as ORM; // Annotations ORM
use Symfony\Component\Validator\Constraints as Assert; // Contraintes de validation Symfony
use ApiPlatform\Metadata\ApiResource; // Annotation API Platform
use Symfony\Component\Serializer\Annotation\MaxDepth; // Limitation profondeur sérialisation

#[ORM\Entity(repositoryClass: LocationRepository::class)] // Déclaration d'entité Doctrine avec repository personnalisé
#[ORM\HasLifecycleCallbacks] // Permet d'utiliser les callbacks du cycle de vie (ex: PrePersist, PreUpdate)
#[ApiResource] // Expose cette entité via API Platform
class Location
{
    #[ORM\Id] // Identifiant unique
    #[ORM\GeneratedValue] // Auto-incrémenté
    #[ORM\Column] // Colonne standard
    private ?int $id = null;

    #[ORM\Column(length: 100)] // Colonne string max 100 caractères pour la ville
    #[Assert\NotBlank] // Validation : ne doit pas être vide
    #[Assert\Length(min: 2, max: 100)] // Validation : longueur minimale et maximale
    private ?string $city = null;

    #[ORM\Column(length: 100)] // Colonne string max 100 caractères pour le pays
    #[Assert\NotBlank] // Validation : ne doit pas être vide
    #[Assert\Length(min: 2, max: 100)] // Validation : longueur min/max
    private ?string $country = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)] // Colonne float nullable pour latitude
    #[Assert\Range(min: -90, max: 90, notInRangeMessage: 'Latitude must be between -90 and 90 degrees')] // Validation plage latitude valide
    private ?float $latitude = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)] // Colonne float nullable pour longitude
    #[Assert\Range(min: -180, max: 180, notInRangeMessage: 'Longitude must be between -180 and 180 degrees')] // Validation plage longitude valide
    private ?float $longitude = null;

    #[ORM\Column] // Colonne datetime immutable pour date de création
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)] // Colonne datetime immutable nullable pour date de mise à jour
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(mappedBy: 'location', targetEntity: Act::class, cascade: ['persist', 'remove'])] // Relation OneToOne avec Act, en cascade persist & remove
    #[MaxDepth(1)] // Limite profondeur sérialisation pour éviter récursion infinie
    private ?Act $act = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(); // Initialise la date de création à la date actuelle
    }

    // Getter de l'id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter de la ville
    public function getCity(): ?string
    {
        return $this->city;
    }

    // Setter de la ville
    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    // Getter du pays
    public function getCountry(): ?string
    {
        return $this->country;
    }

    // Setter du pays
    public function setCountry(string $country): static
    {
        $this->country = $country;
        return $this;
    }

    // Getter de la latitude
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    // Setter de la latitude (nullable)
    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    // Getter de la longitude
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    // Setter de la longitude (nullable)
    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;
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

    // Getter de l'act lié (relation OneToOne)
    public function getAct(): ?Act
    {
        return $this->act;
    }

    // Setter de l'act lié
    public function setAct(?Act $act): static
    {
        $this->act = $act;
        return $this;
    }

    /**
     * Setter de la date de création (utile pour cas spécifiques)
     *
     * @return  self
     */ 
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Setter de la date de mise à jour
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Retourne une représentation string de la location (ville + pays)
    public function __toString(): string
    {
        return $this->city . ' (' . $this->country . ')';
    }
}
