<?php

namespace App\Controller;

use App\DTO\PlayerBindingDTO;
use App\Entity\GameSession;
use App\Entity\Player;
use App\Service\GameSessionService;
use App\Validation\PayloadValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('game/session', name: 'gameSession')]
class GameSessionController extends AbstractController
{
    private const UUID_REGEX = "[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}";

    private GameSessionService $gameSessionService;

    public function __construct(GameSessionService $gameSessionService)
    {
        $this->gameSessionService = $gameSessionService;
    }

    #[Route('test', name: 'test', methods: ['GET'])]
    public function testingRoutes(Request $request): JsonResponse
    {
        return $this->json(["fine","and", "good"]);
    }

    #[Route('/{sessionId}', name: 'Get', requirements: ['sessionId' => self::UUID_REGEX], methods: ['GET'], stateless: true)]
    public function getSession(Request $request, string $sessionId): JsonResponse | RedirectResponse
    {
        $session = $this->gameSessionService->findOrCreateSession($sessionId);
        if ($sessionId !== $session->uuid) {
            return $this->redirectToRoute("gameSessionGet", ["sessionId" => $session->uuid]);
        }
        return $this->json(data: [
            'sessionId' => $session->id,
            'uuid' => $session->uuid,
            'createdAt' => $session->createdAt->format('c'),
            'expiresAt' => $session->expiresAt->format('c'),
            'isExpired' => $session->isExpired()
        ],
            headers: [
                'Set-Cookie' => 'gameSessionId=' . $session->uuid,
            ]
        );
    }

    #[Route('/{sessionId}/bind-player', name: 'bind_player_to_session', methods: ['POST'])]
    public function bindPlayerToSession(
        Request $request,
        PayloadValidator $payloadValidator,
        EntityManagerInterface $entityManager,
        string $sessionId
    ): JsonResponse {
        $validationResult = $payloadValidator->validate($request, PlayerBindingDTO::class);
        if ($validationResult['errors']) {
            $errorResponse = $payloadValidator->createErrorResponse($validationResult['errors']);
            return $this->json(['errors' => $errorResponse], JsonResponse::HTTP_BAD_REQUEST);
        }
        /* @var $dto PlayerBindingDTO */
        $dto = $validationResult['dto'];

        $session = $entityManager->getRepository(GameSession::class)->findOneBy(['uuid' => $sessionId]);
        if (!$session) {
            return $this->json(['message' => 'Session not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($session->player) {
            return $this->json(['message' => 'Session already occupied'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $player = null;
        if ($dto->uuid) {
            $player = $entityManager->getRepository(Player::class)->findOneBy(['uuid' => $dto->uuid]);
        }

        if (!$player) {
            $playerUuid = Uuid::isValid($dto->uuid ?? '') ? $dto->uuid : Uuid::v4()->toRfc4122();
            $player = new Player($playerUuid, $dto->name, $dto->email, $dto->trackingIds);
            $entityManager->persist($player);
        }

        $session->bindPlayer($player);
        $entityManager->flush();

        return $this->json(['playerId' => $player->uuid, "sessionId" => $session->uuid]);
    }
}
