<?php

// Déclare le namespace, c’est-à-dire l’organisation logique du code dans Symfony
namespace App\Controller\Admin;

// On importe l'entité "Contact" qui sera administrée avec EasyAdmin
use App\Entity\Contact;

// On importe différents types de champs pour créer les formulaires dans EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;           // Champ ID (clé primaire)
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;         // Champ texte simple (ligne unique)
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;        // Champ pour adresse email
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;       // Champ liste déroulante (menu de choix)
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;     // Champ date et heure
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;     // Champ texte multilignes

// Type de champ utilisé pour personnaliser un champ Textarea avec Symfony
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

// Contrôleur de base EasyAdmin qu’on doit étendre pour créer un CRUD personnalisé
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

// Cette classe configure l’administration de l’entité Contact
class ContactCrudController extends AbstractCrudController
{
    // Cette méthode indique à EasyAdmin l'entité que ce CRUD doit gérer
    public static function getEntityFqcn(): string
    {
        // Retourne le nom complet (FQCN) de l'entité gérée
        return Contact::class;
    }
    // Cette méthode configure les champs à afficher dans EasyAdmin selon la page (index, edit, new)
    public function configureFields(string $pageName): iterable
    {
        // Retourne une liste (tableau) de champs à afficher
        return [
            // Champ ID : affiché uniquement dans la liste (non modifiable)
            IdField::new('id')->onlyOnIndex(),
            // Champ pour le prénom de la personne
            TextField::new('firstName')
                ->setLabel('Prénom')           // Libellé affiché dans le formulaire
                ->setRequired(true)            // Rend ce champ obligatoire
                ->setMaxLength(100),           // Limite à 100 caractères maximum
            // Champ pour le nom de la personne
            TextField::new('name')
                ->setLabel('Nom')
                ->setRequired(true)
                ->setMaxLength(100),
            // Champ pour l'adresse email
            EmailField::new('email')
                ->setLabel('Email')
                ->setRequired(true) // Ce champ est obligatoire
                ->setFormTypeOption('attr', ['maxlength' => 180]) // Limite HTML
                ->setFormTypeOption('constraints', [              // Contraintes Symfony
                    new \Symfony\Component\Validator\Constraints\Email(),     // Doit être un email valide
                    new \Symfony\Component\Validator\Constraints\NotBlank(), // Ne peut pas être vide
                ]),
            // Champ pour le sujet du message, avec une liste de choix prédéfinis
            ChoiceField::new('subject')
                ->setLabel('Sujet')
                ->setChoices([ // Liste des options : "visible" => "valeur enregistrée"
                    'Problème technique' => 'problème technique',
                    'Question' => 'question',
                    'Suggestion' => 'suggestion',
                ]),
            // Champ pour le message (multiligne)
            TextareaField::new('message')
                ->setLabel('Message') // Libellé dans le formulaire
                ->setRequired(true)   // Champ obligatoire
                ->setMaxLength(2000)  // Longueur maximale côté formulaire admin
                ->setFormType(TextareaType::class) // Spécifie le type Symfony utilisé
                ->setFormTypeOption('attr', ['rows' => 5]) // Affiche 5 lignes dans le champ
                ->setFormTypeOption('constraints', [ // Contraintes de validation Symfony
                    new \Symfony\Component\Validator\Constraints\NotBlank(), // Ne peut pas être vide
                    new \Symfony\Component\Validator\Constraints\Length([
                        'min' => 10,   // Minimum 10 caractères
                        'max' => 2000, // Maximum 2000 caractères
                    ]),
                ]),
            // Champ affichant la date de création du message (lecture seule)
            DateTimeField::new('createdAt')
                ->setLabel('Date de création')
                ->onlyOnIndex(), // Visible uniquement dans la liste
            // Champ affichant la date de mise à jour (lecture seule)
            DateTimeField::new('updatedAt')
                ->setLabel('Date de mise à jour')
                ->onlyOnIndex() // Visible seulement dans la liste
                ->setFormTypeOption('disabled', true) // Empêche la modification
        ];
    }
}
