<?php
namespace App\Service;

use App\Entity\GameSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GameSessionService
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly CacheInterface $cache)
    {
    }

    public function findOrCreateSession(string $sessionId): GameSession
    {
        return $this->cache->get($sessionId, function (ItemInterface $item) use ($sessionId) {
            $item->expiresAfter(3_600);

            $session = $this->entityManager->getRepository(GameSession::class)->findOneBy(['uuid' => $sessionId]);

            if (!$session) {
                $session = new GameSession(Uuid::v7()->toRfc4122());
                $this->entityManager->persist($session);
                $this->entityManager->flush();

                if ($sessionId !== $session->uuid) {
                    $item->expiresAfter(1);
                }
            }

            return $session;
        });
    }

    public function renewSession(GameSession $gameSession): GameSession
     {
         $result = new GameSession(Uuid::v7()->toRfc4122());
         $result->previousSession = $gameSession;
         $result->player = $gameSession->player;

         $this->entityManager->persist($result);

         return $result;
     }

    public function clearCache(GameSession $session): void
    {
        $this->cache->delete($session->uuid);
    }
}
