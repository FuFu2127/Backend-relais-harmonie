<?php

// Déclare le namespace : l'emplacement logique de cette classe dans ton projet Symfony
namespace App\Controller\Admin;

// Importe l'entité Comment qui sera gérée dans l'administration
use App\Entity\Comment;

// Importe les types de champs que EasyAdmin utilise dans ses formulaires
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField; // Champ pour l'identifiant
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField; // Champ pour les dates
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField; // Champ texte multilignes
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField; // Champ pour les relations (entités liées)
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; // Contrôleur de base EasyAdmin

// Déclare la classe CommentCrudController pour configurer l'admin de l'entité Comment
class CommentCrudController extends AbstractCrudController
{
    // Méthode obligatoire : indique à EasyAdmin quelle entité ce CRUD gère
    public static function getEntityFqcn(): string
    {
        return Comment::class; // Ici, c'est l'entité Comment
    }

    // Configure les champs affichés dans le formulaire et la liste
    public function configureFields(string $pageName): iterable
    {
        return [
            // Affiche le champ ID uniquement sur la page d'index (liste), pas en création/édition
            IdField::new('id')->onlyOnIndex(),

            // Champ pour le contenu du commentaire (texte long)
            TextareaField::new('content')
                ->setLabel('Contenu') // Nom affiché dans le formulaire
                ->setRequired(true) // Rend le champ obligatoire
                ->setMaxLength(1000) // Limite à 1000 caractères
                ->setHelp('Le contenu du commentaire ne doit pas dépasser 1000 caractères'), // Message d'aide

            // Champ pour l'association avec l'utilisateur (auteur du commentaire)
            AssociationField::new('user')
                ->setLabel('Auteur') // Nom affiché
                ->setRequired(true), // Champ obligatoire

            // Champ pour l'association avec l'acte (act) lié au commentaire
            AssociationField::new('act')
                ->setLabel('Acte') // Nom affiché
                ->setRequired(true), // Champ obligatoire

            // Affiche la date de création du commentaire uniquement dans la liste
            DateTimeField::new('createdAt')
                ->setLabel('Date de création')
                ->onlyOnIndex(),

            // Affiche la date de mise à jour uniquement dans la liste, en lecture seule
            DateTimeField::new('updatedAt')
                ->setLabel('Date de mise à jour')
                ->onlyOnIndex()
                ->setFormTypeOption('disabled', true) // Empêche la modification de ce champ
        ];
    }
}