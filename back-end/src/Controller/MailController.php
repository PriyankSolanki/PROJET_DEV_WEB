<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Repository\MailRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[Route('/api/mails', name: 'api_mails_')]
class MailController extends AbstractController
{

    #[OA\Get(
        path: '/api/mails/{microsoftId}',
        summary: 'Récupère tous les mails d’un utilisateur par Microsoft ID',
        parameters: [
            new OA\Parameter(
                name: 'microsoftId',
                description: 'Identifiant Microsoft unique de l’utilisateur',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des mails',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'subject', type: 'string'),
                            new OA\Property(property: 'sender', type: 'string'),
                            new OA\Property(property: 'recipients', type: 'array', items: new OA\Items(type: 'string')),
                            new OA\Property(property: 'body', type: 'string'),
                            new OA\Property(property: 'receivedAt', type: 'string', format: 'date-time')
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(response: 404, description: 'Utilisateur non trouvé')
        ]
    )]
    #[Route('/{microsoftId}', methods: ['GET'])]
    public function index(MailRepository $mailRepository, UserRepository $userRepository, string $microsoftId): JsonResponse
    {

        $user = $userRepository->findOneBy(['microsoftId' => $microsoftId]);
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $mails = $mailRepository->findBy(['user' => $user]);

        $data = array_map(function (Mail $mail) {
            return [
                'id' => $mail->getId(),
                'subject' => $mail->getSubject(),
                'sender' => $mail->getSender(),
                'recipients' => $mail->getRecipients(),
                'body' => $mail->getBody(),
                'receivedAt' => $mail->getReceivedAt()->format('Y-m-d H:i:s')
            ];
        }, $mails);

        return $this->json($data);
    }


    #[OA\Get(
        path: '/api/mails/{messageId}',
        summary: 'Récupère un mail à partir de son messageId',
        parameters: [
            new OA\Parameter(
                name: 'messageId',
                description: 'Identifiant unique du message (Microsoft Graph)',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Mail trouvé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'subject', type: 'string'),
                        new OA\Property(property: 'sender', type: 'string'),
                        new OA\Property(property: 'recipients', type: 'array', items: new OA\Items(type: 'string')),
                        new OA\Property(property: 'body', type: 'string'),
                        new OA\Property(property: 'receivedAt', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'user', type: 'integer')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Mail non trouvé'
            )
        ]
    )]
    #[Route('/{messageId}', methods: ['GET'])]
    public function show(MailRepository $mailRepository, string $messageId): JsonResponse
    {
        $mail = $mailRepository->findOneBy(['messageId' => $messageId]);
        if (!$mail) {
            return $this->json(['error' => 'Mail not found'], Response::HTTP_BAD_REQUEST);
        }
        return $this->json([
            'id' => $mail->getId(),
            'subject' => $mail->getSubject(),
            'sender' => $mail->getSender(),
            'recipients' => $mail->getRecipients(),
            'body' => $mail->getBody(),
            'receivedAt' => $mail->getReceivedAt()?->format('Y-m-d H:i:s'),
            'user' => $mail->getUser()?->getId(),
        ]);
    }


    #[OA\Post(
        path: '/api/mails',
        summary: 'Créer un nouveau mail lié à un utilisateur Microsoft',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['sender', 'recipients', 'receivedAt', 'messageId', 'user'],
                properties: [
                    new OA\Property(property: 'subject', type: 'string', example: 'Re: Projet X'),
                    new OA\Property(property: 'sender', type: 'string', example: 'alice@example.com'),
                    new OA\Property(property: 'recipients', type: 'array', items: new OA\Items(type: 'string'), example: ['bob@example.com']),
                    new OA\Property(property: 'body', type: 'string', example: 'Voici le contenu du mail...'),
                    new OA\Property(property: 'receivedAt', type: 'string', format: 'date-time', example: '2025-07-02T12:34:56'),
                    new OA\Property(property: 'messageId', type: 'string', example: 'abc-123456'),
                    new OA\Property(
                        property: 'user',
                        type: 'object',
                        required: ['microsoftId'],
                        properties: [
                            new OA\Property(property: 'microsoftId', type: 'string', example: 'user-1234')
                        ]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Mail créé avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 42)
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Erreur dans les données envoyées'),
            new OA\Response(response: 404, description: 'Utilisateur non trouvé')
        ]
    )]
    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data['user']['microsoftId']) {
            return $this->json(['error' => 'missing [\'user\'][\'microsoftId\'] '], Response::HTTP_BAD_REQUEST);
        }
        if (!$data['sender']) {
            return $this->json(['error' => 'missing [\'sender\'] '], Response::HTTP_BAD_REQUEST);
        }
        if (!$data['recipients']) {
            return $this->json(['error' => 'missing [\'recipients\'] '], Response::HTTP_BAD_REQUEST);
        }
        if (!$data['receivedAt']) {
            return $this->json(['error' => 'missing [\'receivedAt\'] '], Response::HTTP_BAD_REQUEST);
        }
        if (!$data['messageId']) {
            return $this->json(['error' => 'missing [\'messageId\'] '], Response::HTTP_BAD_REQUEST);
        }
        $user = $userRepo->findOneBy(['microsoftId' => $data['user']['microsoftId']]);
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $mail = new Mail();
        $mail->setSubject($data['subject'] ?? 'Aucun objet');
        $mail->setSender($data['sender'] ?? '');
        $mail->setRecipients($data['recipients'] ?? []);
        $mail->setBody($data['body'] ?? 'Aucun contenu');
        $mail->setReceivedAt(new DateTime($data['receivedAt'] ?? 'now'));
        $mail->setMessageId($data['messageId'] ?? uniqid());
        $mail->setUser($user);

        $em->persist($mail);
        $em->flush();

        return $this->json(['id' => $mail->getId()], Response::HTTP_CREATED);
    }

    #[OA\Delete(
        path: '/api/mails/{messageId}',
        summary: 'Supprimer un mail par son identifiant interne (ID)',
        parameters: [
            new OA\Parameter(
                name: 'messageId',
                in: 'path',
                required: true,
                description: 'Identifiant du mail à supprimer (champ `id` en base)',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Mail supprimé avec succès (aucun contenu)'
            ),
            new OA\Response(
                response: 404,
                description: 'Mail non trouvé'
            )
        ]
    )]
    #[Route('/{messageId}', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, MailRepository $mailRepository, string $messageId): JsonResponse
    {
        $mail = $mailRepository->findOneBy(['id' => $messageId]);
        if (!$mail) {
            return $this->json(['error' => 'Mail not found'], Response::HTTP_NOT_FOUND);
        }
        $em->remove($mail);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }



    #[OA\Patch(
        path: '/api/mails/message/{messageId}',
        summary: 'Met à jour un mail à partir de son messageId (Microsoft)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'subject', type: 'string', example: 'Nouveau sujet'),
                    new OA\Property(property: 'body', type: 'string', example: 'Contenu mis à jour'),
                    new OA\Property(property: 'recipients', type: 'array', items: new OA\Items(type: 'string')),
                    new OA\Property(property: 'receivedAt', type: 'string', format: 'date-time', example: '2025-07-02T14:00:00')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(
                name: 'messageId',
                in: 'path',
                required: true,
                description: 'Identifiant Microsoft du mail à mettre à jour',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Mail mis à jour avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Mail updated'),
                        new OA\Property(property: 'messageId', type: 'string', example: 'abcd1234')
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Mail non trouvé'),
            new OA\Response(response: 400, description: 'Format invalide')
        ]
    )]

    #[Route('/message/{messageId}', name: 'update_by_message_id', methods: ['PATCH'])]
    public function updateMailByMessageId(
        string $messageId,
        Request $request,
        MailRepository $mailRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $mail = $mailRepository->findOneBy(['messageId' => $messageId]);

        if (!$mail) {
            return $this->json(['error' => 'Mail not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['subject'])) {
            $mail->setSubject($data['subject']);
        }

        if (isset($data['body'])) {
            $mail->setBody($data['body']);
        }

        if (isset($data['recipients']) && is_array($data['recipients'])) {
            $mail->setRecipients($data['recipients']);
        }

        if (isset($data['receivedAt'])) {
            try {
                $mail->setReceivedAt(new DateTime($data['receivedAt']));
            } catch (\Exception $e) {
                return $this->json(['error' => 'Invalid receivedAt format'], Response::HTTP_BAD_REQUEST);
            }
        }

        $em->flush();

        return $this->json([
            'message' => 'Mail updated',
            'messageId' => $mail->getMessageId(),
        ]);
    }
}
