<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "game_sessions")]
#[ORM\Index(name: "uuid_idx", fields: ["uuid"])]
class GameSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public readonly int $id;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name: "player_id", referencedColumnName: "id", nullable: true)]
    public ?Player $player;

    public function __construct(
        #[ORM\Column(type: "string")]
        public readonly string $uuid,

        #[ORM\Column(type: "datetime")]
        public readonly \DateTime $createdAt = new \DateTime(),

        #[ORM\Column(type: "datetime")]
        public readonly \DateTime $expiresAt = new \DateTime("+1 hour"),
    )
    {
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function bindPlayer(Player $player): void
    {
        $this->player = $player;
    }
}

