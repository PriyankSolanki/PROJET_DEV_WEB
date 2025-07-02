<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Entity\User;
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
    public function index(MailRepository $mailRepository): JsonResponse
    {
        //TODO
        $mails = $mailRepository->findAll();

        $data = array_map(function (Mail $mail) {
            return [
                'id' => $mail->getId(),
                'subject' => $mail->getSubject(),
                'sender' => $mail->getSender(),
                'recipients' => $mail->getRecipients(),
                'body' => $mail->getBody(),
                'receivedAt' => $mail->getReceivedAt()->format('Y-m-d H:i:s'),
                'user' => $mail->getUser()?->getId(),
            ];
        }, $mails);

        return $this->json($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Mail $mail): JsonResponse
    {
        return $this->json([
            'id' => $mail->getId(),
            'subject' => $mail->getSubject(),
            'sender' => $mail->getSender(),
            'recipients' => $mail->getRecipients(),
            'body' => $mail->getBody(),
            'receivedAt' => $mail->getReceivedAt()->format('Y-m-d H:i:s'),
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

        $user = $userRepo->find($data['user_id'] ?? null);
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_BAD_REQUEST);
        }

        $mail = new Mail();
        $mail->setSubject($data['subject'] ?? '');
        $mail->setSender($data['sender'] ?? '');
        $mail->setRecipients($data['recipients'] ?? []);
        $mail->setBody($data['body'] ?? '');
        $mail->setReceivedAt(new DateTime($data['receivedAt'] ?? 'now'));
        $mail->setMessageId($data['messageId'] ?? uniqid());
        $mail->setUser($user);

        $em->persist($mail);
        $em->flush();

        return $this->json(['id' => $mail->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Mail $mail, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($mail);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
