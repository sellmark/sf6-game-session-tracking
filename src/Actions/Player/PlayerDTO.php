<?php

namespace App\Actions\Player;

use App\Actions\ActionInput;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PlayerDTO implements ActionInput
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public ?string $sessionId = null,

        #[Assert\Uuid]
        public ?string $uuid = null,

        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 100)]
        public ?string $name = null,

        #[Assert\Email]
        public ?string $email = null,

        #[Assert\Type(type: 'array')]
        public ?array $trackingIds = [],
    )
    {
    }
}
