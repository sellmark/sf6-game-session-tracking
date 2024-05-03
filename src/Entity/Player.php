<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "players")]
#[ORM\Index(name: "uuid_idx", fields: ["uuid"])]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public int $id;

    #[ORM\Column(type: "string", unique: true)]
    public string $uuid;

    #[ORM\Column(type: "string", nullable: true)]
    public ?string $name = null;

    #[ORM\Column(type: "string", nullable: true)]
    public ?string $encryptedEmail = null;

    #[ORM\Column(type: "json", nullable: true)]
    public ?array $trackingIds = null;

    public function __construct(
        string $uuid,
        ?string $name = null,
        ?string $encryptedEmail = null,
        ?array $trackingIds = null
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->encryptedEmail = $encryptedEmail;
        $this->trackingIds = $trackingIds;
    }

    // Example method: Update tracking IDs
    public function updateTrackingIds(array $newTrackingIds): void
    {
        $this->trackingIds = array_unique(array_merge($this->trackingIds ?? [], $newTrackingIds));
    }
}