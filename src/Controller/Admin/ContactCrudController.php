<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('firstName')
                ->setLabel('Prénom')
                ->setRequired(true)
                ->setMaxLength(100),
            TextField::new('name')
                ->setLabel('Nom')
                ->setRequired(true)
                ->setMaxLength(100),
            EmailField::new('email')
                ->setLabel('Email')
                ->setRequired(true)
                ->setFormTypeOption('attr', ['maxlength' => 180])
                ->setFormTypeOption('constraints', [
                    new \Symfony\Component\Validator\Constraints\Email(),
                    new \Symfony\Component\Validator\Constraints\NotBlank(),
                ]),
            ChoiceField::new('subject')
                ->setLabel('Sujet')
                ->setChoices([
                    'Problème technique' => 'problème technique',
                    'Question' => 'question',
                    'Suggestion' => 'suggestion',
                ]),
            TextareaField::new('message')
                ->setLabel('Message')
                ->setRequired(true)
                ->setMaxLength(2000)
                ->setFormType(TextareaType::class)
                ->setFormTypeOption('attr', ['rows' => 5])
                ->setFormTypeOption('constraints', [
                    new \Symfony\Component\Validator\Constraints\NotBlank(),
                    new \Symfony\Component\Validator\Constraints\Length(['min' => 10, 'max' => 2000]),
                ]),
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
