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

#[Route('/api/mails', name: 'api_mails_')]
class MailController extends AbstractController
{
    #[Route('{microsoftId}', methods: ['GET'])]
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

    #[Route('/{messageId}', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, MailRepository $mailRepository, string $messageId): JsonResponse
    {
        $mail = $mailRepository->findOneBy(['messageId' => $messageId]);
        if (!$mail) {
            return $this->json(['error' => 'Mail not found'], Response::HTTP_NOT_FOUND);
        }
        $em->remove($mail);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/message/{messageId}', name: 'update_by_message_id', methods: ['PUT', 'PATCH'])]
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
