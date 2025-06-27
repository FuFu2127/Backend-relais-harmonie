<?php

namespace App\Entity; // Namespace de l'entité

use App\Repository\LikesRepository; // Repository associé à l'entité Like
use Doctrine\ORM\Mapping as ORM; // Annotations Doctrine ORM
use Symfony\Component\Validator\Constraints as Assert; // Contraintes de validation Symfony
use ApiPlatform\Metadata\ApiResource; // Ressource API Platform
use Symfony\Component\Serializer\Annotation\MaxDepth; // Annotation pour limiter la profondeur de sérialisation

#[ORM\Entity(repositoryClass: LikesRepository::class)] // Déclare que cette classe est une entité Doctrine liée au repository LikesRepository
#[ORM\Table(name: "likes")] // Nom de la table dans la base de données
#[ORM\UniqueConstraint(name: 'user_act_unique', columns: ['user_id', 'act_id'])] // Contrainte d'unicité pour éviter qu'un utilisateur like deux fois le même act
#[ORM\HasLifecycleCallbacks] // Permet d'utiliser des callbacks lors des événements du cycle de vie de l'entité (ex: PrePersist)
#[ApiResource] // Expose cette entité automatiquement via API Platform
class Like
{
    #[ORM\Id] // Identifiant unique de l'entité
    #[ORM\GeneratedValue] // Auto-incrémenté
    #[ORM\Column] // Colonne standard (int)
    private ?int $id = null;

    #[ORM\Column] // Date de création du like
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'likes')] // Relation ManyToOne vers l'utilisateur (un utilisateur peut avoir plusieurs likes)
    #[ORM\JoinColumn(nullable: false)] // Cette colonne ne peut pas être nulle
    #[Assert\NotNull] // Validation Symfony : ne doit pas être null
    #[MaxDepth(1)] // Limite la profondeur de sérialisation pour éviter les boucles infinies
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'likes')] // Relation ManyToOne vers l'act (un act peut avoir plusieurs likes)
    #[ORM\JoinColumn(nullable: false)] // Colonne non nullable
    #[Assert\NotNull] // Validation : obligatoire
    #[MaxDepth(1)] // Limite profondeur sérialisation
    private ?Act $act = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(); // Initialise la date de création à la date actuelle lors de la création de l'objet
    }

    // Getter de l'id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter de la date de création
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Getter de l'utilisateur lié au like
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Setter de l'utilisateur
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // Getter de l'act lié au like
    public function getAct(): ?Act
    {
        return $this->act;
    }

    // Setter de l'act
    public function setAct(?Act $act): static
    {
        $this->act = $act;
        return $this;
    }

    /**
     * Setter manuel pour la date de création (utile pour certains cas spécifiques)
     *
     * @return  self
     */ 
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
