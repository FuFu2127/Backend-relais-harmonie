<?php

namespace App\Controller\Admin;

use App\Entity\Like;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class LikeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Like::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('user')
                ->setLabel('Utilisateur')
                ->setRequired(true),
            AssociationField::new('act')
                ->setLabel('Acte')
                ->setRequired(true),
            DateTimeField::new('createdAt')
                ->setLabel('Date de création')
                ->onlyOnIndex(),
        ];
    }
    
}
