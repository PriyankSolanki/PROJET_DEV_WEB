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

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{

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

    #[Route('{microsoftId}', methods: ['POST'])]
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
