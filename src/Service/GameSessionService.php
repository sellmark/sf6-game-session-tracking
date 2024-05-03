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
            $item->expiresAfter(3_600); // Cache for 1 hour

            $session = $this->entityManager->getRepository(GameSession::class)->findOneBy(['uuid' => $sessionId]);

            if (!$session) {
                $session = new GameSession(Uuid::v7()->toRfc4122()); // or recreate with the same ID if necessary
                $this->entityManager->persist($session);
                $this->entityManager->flush();

                if ($sessionId !== $session->uuid) {
                    $item->expiresAfter(1);
                }

                //dispatch GameSessionCreated Event
            }

            return $session;
        });
    }

    public function extendSession(string $sessionId): void
    {
        $session = $this->findOrCreateSession($sessionId);
        $session->extendExpiration();
        $this->entityManager->flush();

        $this->cache->delete($sessionId); // Invalidate the cache to force a refresh on next access
    }
}