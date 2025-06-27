<?php

// Déclare le namespace (l'emplacement logique de la classe dans l'application)
namespace App\Controller\Admin;

// Importe l'entité Challenge à gérer dans l'admin
use App\Entity\Challenge;

// Importe différents types de champs EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField; // Champ pour l'ID (identifiant)
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField; // Champ texte sur une seule ligne
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField; // Champ pour les nombres
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField; // Champ pour les dates et heures
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; // Contrôleur de base EasyAdmin pour une entité

// Déclaration de la classe ChallengeCrudController qui gère l'entité Challenge dans EasyAdmin
class ChallengeCrudController extends AbstractCrudController
{
    // Méthode obligatoire pour indiquer à EasyAdmin quelle entité ce contrôleur admin gère
    public static function getEntityFqcn(): string
    {
        return Challenge::class; // Retourne le nom complet (FQCN) de l'entité Challenge
    }

    // Méthode pour configurer les champs affichés selon la page (liste, édition, création)
    public function configureFields(string $pageName): iterable
    {
        // Retourne un tableau de champs à afficher dans l'interface admin
        return [
            // Affiche l'ID du challenge uniquement sur la page d'index (liste)
            IdField::new('id')->onlyOnIndex(),

            // Champ texte pour le titre du challenge
            TextField::new('title')
                ->setLabel('Titre') // Libellé affiché dans le formulaire
                ->setRequired(true) // Rend ce champ obligatoire
                ->setMaxLength(255), // Limite à 255 caractères

            // Champ numérique pour l'objectif du challenge
            NumberField::new('objective')
                ->setLabel('Objectif') // Libellé affiché
                ->setRequired(true), // Champ obligatoire

            // Champ numérique pour la progression actuelle
            NumberField::new('progress')
                ->setLabel('Progression') // Libellé affiché
                ->setRequired(true) // Champ obligatoire
                ->setHelp('La progression doit être inférieure ou égale à l\'objectif'), // Message d’aide

            // Affiche la date de création, seulement sur la page d’index (lecture seule)
            DateTimeField::new('createdAt')
                ->setLabel('Date de création')
                ->onlyOnIndex(),

            // Affiche la date de mise à jour, seulement sur la page d’index
            DateTimeField::new('updatedAt')
                ->setLabel('Date de mise à jour')
                ->onlyOnIndex()
                ->setFormTypeOption('disabled', true) // Le champ est désactivé (lecture seule même si affiché ailleurs)
        ];
    }
}