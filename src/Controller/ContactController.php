<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact/new', name: 'app_contact_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupération des données JSON
        $data = json_decode($request->getContent(), true);

        // Vérification des données requises
        if (!isset($data['firstName']) || !isset($data['name']) || 
            !isset($data['email']) || !isset($data['subject']) || 
            !isset($data['message'])) {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez remplir tous les champs obligatoires'
            ], 400);
        }

        // Création du contact
        $contact = new Contact();
        $contact->setFirstName($data['firstName']);
        $contact->setName($data['name']);
        $contact->setEmail($data['email']);
        $contact->setSubject($data['subject']);
        $contact->setMessage($data['message']);
        
        // Enregistrement en base de données
        $entityManager->persist($contact);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Message envoyé avec succès !'
        ], 201);
    }
}
