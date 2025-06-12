<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextareaField::new('content')
            ->setLabel('Contenu')
            ->setRequired(true)
            ->setMaxLength(1000)
            ->setHelp('Le contenu du commentaire ne doit pas dépasser 1000 caractères'),
            AssociationField::new('user')
            ->setLabel('Auteur')
            ->setRequired(true),
            AssociationField::new('act')
            ->setLabel('Acte')
            ->setRequired(true),
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
