<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "players")]
#[ORM\Index(name: "uuid_idx", fields: ["uuid"])]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public int $id;

    #[ORM\OneToMany(targetEntity: GameSession::class, mappedBy: "player")]
    public Collection $sessions;

    public function __construct(
        #[ORM\Column(type: "string", unique: true)]
        public string $uuid,
        #[ORM\Column(type: "string", nullable: true)]
        public ?string $name = null,
        #[ORM\Column(type: "string", nullable: true)]
        public ?string $encryptedEmail = null,
        #[ORM\Column(type: "json", nullable: true)]
        public ?array $trackingIds = null
    ) {
        $this->sessions = new ArrayCollection();
    }

    public function updateTrackingIds(array $newTrackingIds): void
    {
        $this->trackingIds = array_unique(array_merge($this->trackingIds ?? [], $newTrackingIds));
    }

    public function getSessions(): Collection {
        return $this->sessions;
    }
}