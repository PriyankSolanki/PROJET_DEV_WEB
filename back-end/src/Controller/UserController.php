<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    #[OA\Get(
        path: '/api/users/{microsoftId}',
        summary: 'Récupère les informations d\'un utilisateur via son microsoftId',
        parameters: [
            new OA\Parameter(
                name: 'microsoftId',
                in: 'path',
                required: true,
                description: 'Identifiant Microsoft de l\'utilisateur',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Utilisateur trouvé',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                        new OA\Property(property: 'displayName', type: 'string', example: 'John Doe'),
                        new OA\Property(property: 'microsoftId', type: 'string', example: 'abc-123-def')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Utilisateur non trouvé'
            )
        ]
    )]
    #[Route('/{microsoftId}', methods: ['GET'])]
    public function show(UserRepository $userRepository, string $microsoftId): JsonResponse
    {
        $user = $userRepository->findOneBy(['microsoftId' => $microsoftId]);
        if(!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'displayName' => $user->getDisplayName(),
            'microsoftId' => $user->getMicrosoftId(),
        ]);
    }

    #[OA\Post(
        path: '/api/users',
        summary: 'Créer un nouvel utilisateur',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'displayName', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'microsoftId', type: 'string', example: 'abc-123-def')
                ],
                required: ['email', 'displayName', 'microsoftId']
            )
        ),
        parameters: [
            new OA\Parameter(
                name: 'microsoftId',
                in: 'path',
                required: true,
                description: 'Identifiant Microsoft (doit correspondre au champ du corps)',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Utilisateur créé avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1)
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Champs requis manquants ou utilisateur déjà existant'
            )
        ]
    )]
    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['displayName'], $data['microsoftId'])) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        if($em->getRepository(User::class)->findOneBy(['microsoftId' => $data['microsoftId']])){
            return $this->json(['error' => 'User already exists'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setDisplayName($data['displayName']);
        $user->setMicrosoftId($data['microsoftId']);

        $em->persist($user);
        $em->flush();

        return $this->json(['id' => $user->getId()], Response::HTTP_CREATED);
    }

    #[OA\Delete(
        path: '/api/users/{microsoftId}',
        summary: 'Supprime un utilisateur par son identifiant Microsoft',
        parameters: [
            new OA\Parameter(
                name: 'microsoftId',
                in: 'path',
                required: true,
                description: 'Identifiant Microsoft de l’utilisateur à supprimer',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Utilisateur supprimé avec succès'
            ),
            new OA\Response(
                response: 404,
                description: 'Utilisateur non trouvé'
            )
        ]
    )]
    #[Route('/{microsoftId}', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, string $microsoftId, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneBy(['microsoftId' => $microsoftId]);
        if(!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $em->remove($user);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
