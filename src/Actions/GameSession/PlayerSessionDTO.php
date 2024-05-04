<?php

namespace App\Actions\GameSession;

use App\Actions\ActionOutput;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PlayerSessionDTO implements ActionOutput
{
    public function __construct(
        public int $id,

        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $playerId,

        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $sessionId
    )
    {
    }
}
