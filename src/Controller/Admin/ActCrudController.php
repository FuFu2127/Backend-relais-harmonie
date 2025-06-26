<?php

// Déclare le namespace (emplacement logique de la classe)
namespace App\Controller\Admin;

// Importe l'entité Act
use App\Entity\Act;

// Importe les différents types de champs fournis par EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField; // Champ pour afficher l'identifiant (ID)
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField; // Champ texte (une seule ligne)
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField; // Champ de type choix (liste déroulante)
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField; // Champ de type date/heure
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField; // Champ texte multi-lignes
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField; // Champ pour les relations entre entités (ManyToOne, etc.)
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; // Contrôleur de base pour une page CRUD

// Déclaration de la classe ActCrudController qui gère l'interface admin de l'entité Act
class ActCrudController extends AbstractCrudController
{
    // Méthode obligatoire pour indiquer à EasyAdmin quelle entité est gérée par ce contrôleur
    public static function getEntityFqcn(): string
    {
        return Act::class; // Retourne la classe complète (FQCN) de l'entité
    }

    // Méthode qui configure les champs affichés dans le formulaire et dans la liste
    public function configureFields(string $pageName): iterable
    {
        // On retourne une liste de champs à afficher
        return [
            // Affiche le champ "id" uniquement sur la page d'index (liste)
            IdField::new('id')->onlyOnIndex(),

            // Champ pour le titre de l'acte
            TextField::new('title')
                ->setLabel('Titre') // Libellé affiché
                ->setRequired(true) // Champ obligatoire
                ->setMaxLength(255), // Longueur max du texte autorisé

            // Champ pour la description (multi-ligne)
            TextareaField::new('description')
                ->setLabel('Description')
                ->setRequired(true)
                ->setMaxLength(2000) // Longueur max de la description
                ->setFormTypeOption('attr', ['rows' => 5]), // Affiche 5 lignes dans le textarea

            // Champ pour la catégorie (liste de choix)
            ChoiceField::new('category')
                ->setLabel('Catégorie')
                ->setRequired(true)
                ->setHelp('Sélectionnez une catégorie pour cet acte') // Message d'aide
                ->setChoices([
                    'Solidarité' => 'Solidarité',
                    'Nature' => 'Nature',
                    'Spiritualité' => 'Spiritualité',
                    'Culture' => 'Culture',
                    'Animaux' => 'Animaux',
                    'Partage' => 'Partage',
                    'Inspiration' => 'Inspiration',
                ]),

            // Champ pour l'URL d'une image
            TextField::new('imgUrl')
                ->setLabel('URL de l\'image')
                ->setRequired(false) // Ce champ est optionnel
                ->setMaxLength(255)
                ->setHelp('URL d\'une image pour illustrer l\'acte'),

            // Champ pour la relation avec une localisation (autre entité)
            AssociationField::new('location')
                ->setLabel('Localisation')
                ->setRequired(false) // La localisation est facultative
                ->setHelp('Sélectionnez une localisation (optionnel)'),

            // Champ pour la relation avec l'utilisateur qui a créé l'acte
            AssociationField::new('user')
                ->setLabel('Utilisateur')
                ->setFormTypeOption('required', true), // L'utilisateur est requis

            // Champ pour la relation avec un challenge (facultatif)
            AssociationField::new('challenge')
                ->setLabel('Challenge')
                ->setRequired(false),

            // Champ date/heure pour afficher la date de création (visible uniquement sur la liste)
            DateTimeField::new('createdAt')
                ->setLabel('Date de création')
                ->onlyOnIndex(),

            // Champ date/heure pour afficher la date de mise à jour (visible uniquement sur la liste)
            DateTimeField::new('updatedAt')
                ->setLabel('Date de mise à jour')
                ->onlyOnIndex()
        ];
    }
}
