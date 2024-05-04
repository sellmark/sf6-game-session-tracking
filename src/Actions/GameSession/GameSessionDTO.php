<?php

namespace App\Actions\GameSession;

use App\Actions\ActionInput;
use App\Actions\ActionOutput;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GameSessionDTO implements ActionInput, ActionOutput
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $uuid,

        #[Assert\NotBlank]
        public bool $isExpired,

        #[Assert\Uuid]
        #[Assert\NotBlank]
        public ?string $previousUuid = null,
    )
    {
    }
}
