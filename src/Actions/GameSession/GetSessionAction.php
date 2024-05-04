<?php

namespace App\Actions\GameSession;

use App\Actions\Action;
use App\Actions\ActionInput;
use App\Actions\ActionOutput;
use App\Service\GameSessionService;

final class GetSessionAction extends Action {

    public function __construct(
        private readonly GameSessionService $gameSessionService,
    )
    {
    }

    /* @var $input GameSessionDTO */
    public function execute(ActionInput $input): ActionOutput
    {
        $session = $this->gameSessionService->findOrCreateSession($input->uuid);

        if ($session->isExpired()) {
            $this->gameSessionService->clearCache($session);
            $session = $this->gameSessionService->renewSession($session);
        }

        return new GameSessionDTO($session->uuid, $session->isExpired());
    }
}
