<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GameSessionTest extends WebTestCase
{
    public function testTestingRoutes(): void
    {
        $client = static::createClient();
        $client->request('GET', '/game/session/test');

        $this->assertResponseIsSuccessful();
        $this->assertEquals('["fine","and","good"]', $client->getResponse()->getContent());
    }

    public function testGetSessionInvalidUUID(): void
    {
        $client = static::createClient();
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $client->request('GET', '/game/session/' . $uuid);

        $code = $client->getResponse()->getStatusCode();
        $this->assertEquals($code,Response::HTTP_FOUND);
    }

    public function testBindPlayerToSession(): void
    {
        $client = static::createClient();
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $client->request('GET', '/game/session/' . $uuid);
        $response = $client->getResponse()->getContent();
        $data = json_decode($response, true);
        $sessionId = $data['uuid'];

        $client->request('POST', '/game/session/bind-player', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Marek',
            'email' => 'm@sellmark.pl',
            'sessionId' => $sessionId
        ]));


        $response = $client->getResponse()->getContent();

        $data = json_decode($response, true);

        $this->assertEquals($sessionId, $data['sessionId']);
        $this->assertResponseIsSuccessful();
    }

    public function testBindPlayerToSessionWithError(): void
    {
        $client = static::createClient();
        $client->request('POST', '/game/session/bind-player', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'sessionId' => 'invalid-uuid',
            'playerId' => 'player123'
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
