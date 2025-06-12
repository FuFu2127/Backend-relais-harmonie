<?php

namespace App\Controller\Admin;

use App\Entity\Act;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ActCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Act::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title')
                ->setLabel('Titre')
                ->setRequired(true)
                ->setMaxLength(255),
            TextareaField::new('description')
                ->setLabel('Description')
                ->setRequired(true)
                ->setMaxLength(2000)
                ->setFormTypeOption('attr', ['rows' => 5]),
            ChoiceField::new('category')
                ->setLabel('Catégorie')
                ->setRequired(true)
                ->setHelp('Sélectionnez une catégorie pour cet acte')
                ->setChoices([
                    'Solidarité' => 'Solidarité',
                    'Nature' => 'Nature',
                    'Spiritualité' => 'Spiritualité',
                    'Culture' => 'Culture',
                    'Animaux' => 'Animaux',
                    'Partage' => 'Partage',
                    'Inspiration' => 'Inspiration',
                ]),
            TextField::new('imgUrl')
                ->setLabel('URL de l\'image')
                ->setRequired(false)
                ->setMaxLength(255)
                ->setHelp('URL d\'une image pour illustrer l\'acte'),
            AssociationField::new('location')
                ->setLabel('Localisation')
                ->setRequired(false)
                ->setHelp('Sélectionnez une localisation (optionnel)'),
            AssociationField::new('user')
                ->setLabel('Utilisateur')
                ->setFormTypeOption('required', true),
            AssociationField::new('challenge')
                ->setLabel('Challenge')
                ->setRequired(false),
            DateTimeField::new('createdAt')
                ->setLabel('Date de création')
                ->onlyOnIndex(),
            DateTimeField::new('updatedAt')
                ->setLabel('Date de mise à jour')
                ->onlyOnIndex()
            
        ];
    }
    
}
