<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('pseudo')
                ->setLabel('Pseudo')
                ->setRequired(true)
                ->setMaxLength(50),
            EmailField::new('email')
                ->setLabel('Email')
                ->setRequired(true)
                ->setFormTypeOption('attr', ['maxlength' => 180])
                ->setFormTypeOption('constraints', [
                    new \Symfony\Component\Validator\Constraints\Email(),
                    new \Symfony\Component\Validator\Constraints\NotBlank(),
                ]),
            TextField::new('password')
                ->setLabel('Mot de passe')
                ->setRequired(true)
                ->setFormType(PasswordType::class)
                ->setFormTypeOption('constraints', [
                    new \Symfony\Component\Validator\Constraints\NotBlank(),
                    new \Symfony\Component\Validator\Constraints\Length(['min' => 6]),
                ]),
            TextField::new('imgUrl')
                ->setLabel('URL de l\'image')
                ->setRequired(false)
                ->setMaxLength(255)
                ->setHelp('URL d\'une image pour le profil'),
            DateTimeField::new('birthday')
                ->setLabel('Date de naissance')
                ->setRequired(false)
                ->setHelp('Format : YYYY-MM-DD'),
            DateTimeField::new('createdAt')
                ->setLabel('Date de création')
                ->onlyOnIndex(),
            DateTimeField::new('updatedAt')
                ->setLabel('Date de mise à jour')
                ->onlyOnIndex()
        ];
    }
    
}
