<?php

namespace App\Actions\Player;

use App\Actions\Action;
use App\Actions\ActionInput;
use App\Actions\ActionOutput;
use App\Actions\GameSession\PlayerSessionDTO;
use App\Service\GameSessionService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;

final class BindPlayerAction extends Action {

    public function __construct(
        private readonly PlayerService $playerService,
        private readonly GameSessionService $gameSessionService,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    /* @var $input PlayerDTO */
    public function execute(ActionInput $input): ActionOutput
    {
        $player = $this->playerService->getPlayer($input);
        $session = $this->gameSessionService->findOrCreateSession($input->sessionId);

        if ($session->isExpired()) {
            $session = $this->gameSessionService->renewSession($session);
        }



        if (!$session->getPlayer()) {
            $session->bindPlayer($player);
            $this->entityManager->flush();
        }


        return new PlayerSessionDTO($session->id, $player->uuid, $session->uuid);
    }
}
