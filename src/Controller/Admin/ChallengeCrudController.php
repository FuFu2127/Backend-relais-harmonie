<?php

namespace App\Controller\Admin;

use App\Entity\Challenge;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ChallengeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Challenge::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title')
                ->setLabel('Titre')
                ->setRequired(true)
                ->setMaxLength(255),
            NumberField::new('objective')
                ->setLabel('Objectif')
                ->setRequired(true),
            NumberField::new('progress')
                ->setLabel('Progression')
                ->setRequired(true)
                ->setHelp('La progression doit être inférieure ou égale à l\'objectif'),
            DateTimeField::new('createdAt')
                ->setLabel('Date de création')
                ->onlyOnIndex(),
            DateTimeField::new('updatedAt')
                ->setLabel('Date de mise à jour')
                ->onlyOnIndex()
                ->setFormTypeOption('disabled', true)
        ];
    }
    
}
