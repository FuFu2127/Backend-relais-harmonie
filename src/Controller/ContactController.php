<?php

// Déclaration du namespace du contrôleur
namespace App\Controller;

// Import de l'entité Contact pour manipuler les contacts en base
use App\Entity\Contact;

// Import de l'EntityManager pour gérer la persistance en base de données
use Doctrine\ORM\EntityManagerInterface;

// Import du contrôleur de base Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Import de la classe Request pour gérer la requête HTTP entrante
use Symfony\Component\HttpFoundation\Request;

// Import de la classe JsonResponse pour renvoyer des réponses JSON
use Symfony\Component\HttpFoundation\JsonResponse;

// Import de l'annotation Route pour définir les routes via attributs PHP 8
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    // Définition d'une route POST accessible via /contact/new avec le nom "app_contact_new"
    #[Route('/contact/new', name: 'app_contact_new', methods: ['POST'])]
    public function new(
        Request $request,                   // Injection de la requête HTTP
        EntityManagerInterface $entityManager // Injection de l'EntityManager pour la base de données
    ): JsonResponse                        // Retourne une réponse JSON
    {
        // Récupération des données JSON envoyées dans le corps de la requête
        $data = json_decode($request->getContent(), true);

        // Vérification que toutes les données requises sont présentes dans le tableau
        if (!isset($data['firstName']) || !isset($data['name']) || 
            !isset($data['email']) || !isset($data['subject']) || 
            !isset($data['message'])) {
            
            // Si un champ obligatoire manque, on renvoie une réponse JSON d'erreur avec code 400 (mauvaise requête)
            return $this->json([
                'success' => false,
                'message' => 'Veuillez remplir tous les champs obligatoires'
            ], 400);
        }

        // Création d'une nouvelle instance de l'entité Contact
        $contact = new Contact();

        // Remplissage des propriétés de l'entité avec les données reçues
        $contact->setFirstName($data['firstName']);
        $contact->setName($data['name']);
        $contact->setEmail($data['email']);
        $contact->setSubject($data['subject']);
        $contact->setMessage($data['message']);
        
        // Demande à Doctrine de persister (enregistrer) l'entité Contact en base de données
        $entityManager->persist($contact);

        // Exécution de la requête d'insertion en base (commit)
        $entityManager->flush();

        // Retourne une réponse JSON indiquant le succès avec un code HTTP 201 (créé)
        return $this->json([
            'success' => true,
            'message' => 'Message envoyé avec succès !'
        ], 201);
    }
}
