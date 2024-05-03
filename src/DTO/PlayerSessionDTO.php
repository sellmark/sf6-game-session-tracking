<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class PlayerSessionDTO
{
    public function __construct(
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
