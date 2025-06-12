<?php

namespace App\Controller\Admin;

use App\Entity\Chain;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ChainCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chain::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('invitationToken')
                ->setLabel('Token d\'invitation')
                ->setHelp('Doit être unique, min 6 caractères'),
            DateTimeField::new('createdAt')
                ->setLabel('Créé le')
                ->onlyOnDetail(),
            DateTimeField::new('updatedAt')
                ->setLabel('Mis à jour le')
                ->onlyOnDetail(),
            AssociationField::new('act')
                ->setLabel('Acte associé')
                ->onlyOnDetail(),
        ];
    }
    
}
