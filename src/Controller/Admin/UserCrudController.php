<?php

// Namespace pour organiser ce contrôleur dans la partie Admin
namespace App\Controller\Admin;

// Import de l'entité User que ce CRUD va gérer
use App\Entity\User;

// Import des champs EasyAdmin nécessaires pour construire les formulaires
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;          // Champ ID (clé primaire)
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;        // Champ texte simple
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;       // Champ email
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;    // Champ date/heure
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField; // Champ relation (ManyToOne, OneToMany, etc.)

// Import du type de champ Password pour masquer la saisie du mot de passe
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

// Import du contrôleur de base EasyAdmin à étendre pour ce CRUD
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    // Indique à EasyAdmin quelle entité ce contrôleur gère
    public static function getEntityFqcn(): string
    {
        // Retourne la classe complète de l'entité User
        return User::class;
    }

    // Configure les champs à afficher dans les formulaires EasyAdmin
    public function configureFields(string $pageName): iterable
    {
        return [
            // Champ ID, affiché uniquement dans la liste (index), non modifiable
            IdField::new('id')->onlyOnIndex(),

            // Champ texte pour le pseudo de l'utilisateur
            TextField::new('pseudo')
                ->setLabel('Pseudo')     // Label affiché dans le formulaire
                ->setRequired(true)      // Champ obligatoire
                ->setMaxLength(50),      // Longueur maximale autorisée

            // Champ email avec validation intégrée
            EmailField::new('email')
                ->setLabel('Email')
                ->setRequired(true)
                ->setFormTypeOption('attr', ['maxlength' => 180]) // Attribut HTML maxlength
                ->setFormTypeOption('constraints', [               // Contraintes de validation Symfony
                    new \Symfony\Component\Validator\Constraints\Email(),
                    new \Symfony\Component\Validator\Constraints\NotBlank(),
                ]),

            // Champ texte pour le mot de passe avec masque de saisie
            TextField::new('password')
                ->setLabel('Mot de passe')
                ->setRequired(true)
                ->setFormType(PasswordType::class) // Utilise le champ de type password
                ->setFormTypeOption('constraints', [ // Contraintes : obligatoire et min 8 caractères
                    new \Symfony\Component\Validator\Constraints\NotBlank(),
                    new \Symfony\Component\Validator\Constraints\Length(['min' => 8]),
                ]),

            // Champ texte pour l'URL de l'image de profil, facultatif
            TextField::new('imgUrl')
                ->setLabel('URL de l\'image')
                ->setRequired(false)
                ->setMaxLength(255)
                ->setHelp('URL d\'une image pour le profil'), // Aide affichée sous le champ

            // Champ choix multiple pour les rôles utilisateur
            \EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField::new('roles')
                ->setLabel('Rôle')
                ->setHelp('Choisissez un ou plusieurs rôles pour l\'utilisateur')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ])
                ->allowMultipleChoices(true)  // Autorise plusieurs rôles
                ->renderExpanded()             // Affiche les choix sous forme de cases à cocher
                ->renderAsBadges()             // Affiche les rôles sous forme de badges visuels
                ->setFormTypeOption('by_reference', false), // Pour la bonne gestion du tableau de rôles

            // Champ date pour la date de naissance, facultatif
            DateTimeField::new('birthday')
                ->setLabel('Date de naissance')
                ->setRequired(false)
                ->setHelp('Format : YYYY-MM-DD'), // Aide pour le format de saisie

            // Date de création, affichée uniquement dans la liste (index)
            DateTimeField::new('createdAt')
                ->setLabel('Date de création')
                ->onlyOnIndex(),

            // Date de mise à jour, affichée uniquement dans la liste, non modifiable
            DateTimeField::new('updatedAt')
                ->setLabel('Date de mise à jour')
                ->onlyOnIndex(),

            // Association vers les actes liés à cet utilisateur, visible seulement dans la page détail
            AssociationField::new('acts')
                ->setLabel('Actes')
                ->onlyOnDetail()
                ->setHelp('Liste des actes de l\'utilisateur'),

            // Association vers les commentaires écrits par l'utilisateur, visible seulement dans le détail
            AssociationField::new('comments')
                ->setLabel('Commentaires')
                ->onlyOnDetail()
                ->setHelp('Commentaires écrits par l\'utilisateur'),

            // Association vers les likes faits par l'utilisateur, visible seulement dans le détail
            AssociationField::new('likes')
                ->setLabel('Likes')
                ->onlyOnDetail()
                ->setHelp('Likes de l\'utilisateur'),
        ];
    }
}
