<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PlayerBindingDTO
{
    #[Assert\Uuid]
    public ?string $uuid = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 100)]
    public ?string $name = null;

    #[Assert\Email]
    public ?string $email = null;

    #[Assert\Type(type: 'array')]
    public ?array $trackingIds = [];
}