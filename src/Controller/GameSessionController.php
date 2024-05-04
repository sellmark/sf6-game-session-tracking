<?php

namespace App\Controller;

use App\Actions\GameSession\GameSessionDTO;
use App\Actions\GameSession\GetSessionAction;
use App\Actions\Player\BindPlayerAction;
use App\Actions\Player\PlayerDTO;
use App\Service\GameSessionService;
use App\Validation\PayloadValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('game/session', name: 'game_session_')]
class GameSessionController extends AbstractController
{
    private const UUID_REGEX = "[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}";

    public function __construct(private readonly GameSessionService $gameSessionService)
    {
    }

    #[Route('/test', name: 'test', methods: ['GET'])]
    public function testingRoutes(Request $request): JsonResponse
    {
        return $this->json(["fine","and", "good"]);
    }

    #[Route('/{sessionId}', name: 'get', requirements: ['sessionId' => self::UUID_REGEX], methods: ['GET'], stateless: true)]
    public function getSession(
        GetSessionAction $action,
        string $sessionId
    ): JsonResponse {
        $session = $this->gameSessionService->findOrCreateSession($sessionId);
        if ($sessionId !== $session->uuid) {
            return $this->json($action->execute(new GameSessionDTO($session->uuid, $session->isExpired())), JsonResponse::HTTP_FOUND);
        }

        return $this->json($action->execute(new GameSessionDTO($session->uuid, $session->isExpired())));
    }

    #[Route('/bind-player', name: 'bind_player', methods: ['POST'])]
    public function bindPlayerToSession(
        Request $request,
        PayloadValidator $payloadValidator,
        BindPlayerAction $action,
    ): JsonResponse {
        $validationResult = $payloadValidator->validate($request, PlayerDTO::class);
        if ($validationResult['errors']) {
            return $this->json($validationResult, JsonResponse::HTTP_BAD_REQUEST);
        }

        /* @var $input PlayerDTO */
        $input = $validationResult['dto'];

        $session = $this->gameSessionService->findOrCreateSession($input->sessionId);

        if ($session->uuid !== $input->sessionId) {
            return $this->json(['message' => 'Session not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($session->player) {
            return $this->json(['message' => 'Session already occupied'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json($action->execute($input));
    }
}
