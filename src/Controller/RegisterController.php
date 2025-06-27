<?php

// Déclaration du namespace du contrôleur
namespace App\Controller;

// Import de l'entité User pour manipuler les utilisateurs
use App\Entity\User;

// Import de l'EntityManager pour gérer la persistance en base de données
use Doctrine\ORM\EntityManagerInterface;

// Import de la classe Request pour gérer la requête HTTP entrante
use Symfony\Component\HttpFoundation\Request;

// Import de la classe JsonResponse pour renvoyer des réponses JSON
use Symfony\Component\HttpFoundation\JsonResponse;

// Import du service pour hasher les mots de passe des utilisateurs
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Import de l'annotation Route pour définir les routes via attributs PHP 8
use Symfony\Component\Routing\Annotation\Route;

// Import du contrôleur de base Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    // Définition d'une route POST accessible via /api/register avec le nom "api_register"
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,                         // Injection de la requête HTTP
        UserPasswordHasherInterface $passwordHasher, // Injection du service de hash de mot de passe
        EntityManagerInterface $em                // Injection de l'EntityManager pour la base de données
    ): JsonResponse                              // Retourne une réponse JSON
    {
        // Décodage des données JSON envoyées dans le corps de la requête en tableau PHP
        $data = json_decode($request->getContent(), true);

        // Création d'une nouvelle instance de l'entité User
        $user = new User();

        // Affectation du pseudo reçu au nouvel utilisateur
        $user->setPseudo($data['pseudo']);

        // Affectation de l'email reçu au nouvel utilisateur
        $user->setEmail($data['email']);

        // Hashage du mot de passe reçu puis affectation au nouvel utilisateur
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );

        // Demande à Doctrine de persister (enregistrer) l'utilisateur en base de données
        $em->persist($user);

        // Exécution de la requête d'insertion en base (commit)
        $em->flush();

        // Retourne une réponse JSON indiquant que l'utilisateur a bien été créé avec un code HTTP 201 (créé)
        return $this->json(['message' => 'Utilisateur créé !'], 201);
    }
}