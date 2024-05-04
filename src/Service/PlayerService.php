<?php
namespace App\Service;

use App\Actions\Player\PlayerDTO;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class PlayerService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getPlayer(PlayerDTO $player): Player
    {
        $result = null;
        if ($player->uuid) {
            $result = $this->entityManager->getRepository(Player::class)->findOneBy(['uuid' => $player->uuid]);
        }

        if (!$result) {
            $playerUuid = Uuid::isValid($player->uuid ?? '') ? $player->uuid : Uuid::v4()->toRfc4122();
            $result = new Player($playerUuid, $player->name, $player->email, $player->trackingIds);
            $this->entityManager->persist($result);
        }

        return $result;
    }
}
