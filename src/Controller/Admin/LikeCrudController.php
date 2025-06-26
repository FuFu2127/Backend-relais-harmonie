<?php

// Déclare le namespace pour organiser le code dans le dossier Admin
namespace App\Controller\Admin;

// Import de l'entité Like que ce CRUD va gérer
use App\Entity\Like;

// Import du contrôleur de base EasyAdmin à étendre pour créer un CRUD personnalisé
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

// Import des différents types de champs pour le formulaire EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;            // Champ ID (clé primaire)
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;   // Champ pour gérer les relations (ex : ManyToOne)
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;       // Champ pour date et heure

// Définition du contrôleur CRUD pour l'entité Like
class LikeCrudController extends AbstractCrudController
{
    // Méthode qui indique à EasyAdmin quelle entité ce contrôleur admin gère
    public static function getEntityFqcn(): string
    {
        // Retourne le nom complet de la classe de l'entité Like
        return Like::class;
    }

    // Méthode qui configure les champs affichés dans les différentes pages EasyAdmin (liste, édition, création)
    public function configureFields(string $pageName): iterable
    {
        // Retourne un tableau des champs à afficher selon la page
        return [
            // Champ ID, affiché uniquement dans la liste (index), non modifiable
            IdField::new('id')->onlyOnIndex(),

            // Champ pour la relation avec l'utilisateur qui a mis le Like
            AssociationField::new('user')
                ->setLabel('Utilisateur')   // Libellé personnalisé pour ce champ
                ->setRequired(true),         // Champ obligatoire

            // Champ pour la relation avec l’acte (objet du Like)
            AssociationField::new('act')
                ->setLabel('Acte')          // Libellé personnalisé
                ->setRequired(true),         // Champ obligatoire

            // Champ affichant la date de création du Like
            DateTimeField::new('createdAt')
                ->setLabel('Date de création') // Libellé affiché
                ->onlyOnIndex(),                // Visible seulement dans la liste, pas modifiable
        ];
    }
}
