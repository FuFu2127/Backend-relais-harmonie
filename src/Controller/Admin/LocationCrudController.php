<?php

// Déclare le namespace pour organiser ce contrôleur dans l’espace Admin
namespace App\Controller\Admin;

// Import de l'entité Location que ce CRUD va gérer
use App\Entity\Location;

// Import du contrôleur de base EasyAdmin à étendre pour créer un CRUD personnalisé
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

// Import des types de champs EasyAdmin pour construire les formulaires
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;          // Champ ID (clé primaire)
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;        // Champ texte simple (une ligne)
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;      // Champ nombre (décimal)
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;    // Champ date et heure
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField; // Champ relation (ManyToOne, etc.)

// Définition du contrôleur CRUD pour l'entité Location
class LocationCrudController extends AbstractCrudController
{
    // Cette méthode indique à EasyAdmin quelle entité est gérée par ce CRUD
    public static function getEntityFqcn(): string
    {
        // Retourne le nom complet (FQCN) de l’entité Location
        return Location::class;
    }

    // Méthode qui configure les champs affichés dans les différentes pages EasyAdmin (liste, édition, création)
    public function configureFields(string $pageName): iterable
    {
        // Retourne un tableau listant tous les champs à afficher selon la page
        return [
            // Champ ID : affiché uniquement dans la liste (index), non modifiable
            IdField::new('id')->onlyOnIndex(),

            // Champ texte simple pour la ville
            TextField::new('city')->setLabel('Ville'),

            // Champ texte simple pour le pays
            TextField::new('country')->setLabel('Pays'),

            // Champ nombre décimal pour la latitude, optionnel, avec 6 chiffres après la virgule
            NumberField::new('latitude')
                ->setLabel('Latitude')
                ->setNumDecimals(6)
                ->setRequired(false),

            // Champ nombre décimal pour la longitude, optionnel, avec 6 chiffres après la virgule
            NumberField::new('longitude')
                ->setLabel('Longitude')
                ->setNumDecimals(6)
                ->setRequired(false),

            // Champ date et heure pour la date de création, affiché uniquement dans la liste
            DateTimeField::new('createdAt')
                ->setLabel('Date de création')
                ->onlyOnIndex(),

            // Champ date et heure pour la date de mise à jour, affiché uniquement dans la liste, non modifiable
            DateTimeField::new('updatedAt')
                ->setLabel('Date de mise à jour')
                ->onlyOnIndex()
                ->setFormTypeOption('disabled', true),

            // Champ relation vers l'entité Act (un lieu peut être lié à un acte), optionnel
            AssociationField::new('act')
                ->setLabel('Acte')
                ->setRequired(false),
        ];
    }
}