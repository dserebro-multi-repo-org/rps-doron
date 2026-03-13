<?php

namespace App\Controller;

use App\Service\GameService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    public function __construct(
        private readonly GameService $gameService,
        private readonly LoggerInterface $logger,
    ) {}

    #[Route('/games', name: 'create_game', methods: ['POST'])]
    public function createGame(Request $request): JsonResponse
    {
        $this->logger->info('Creating new game');

        try {
            $game = $this->gameService->createGame();
            $this->logger->info('Game created', ['game_id' => $game->id]);
            return $this->json($game->toArray(), 201);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to create game', ['error' => $e->getMessage()]);
            return $this->json(['error' => 'Failed to create game.'], 500);
        }
    }

    #[Route('/games/{id}/moves', name: 'make_move', methods: ['POST'])]
    public function makeMove(string $id, Request $request): JsonResponse
    {
        $body   = json_decode($request->getContent(), associative: true) ?? [];
        $player = $body['player'] ?? null;
        $choice = $body['choice'] ?? null;

        $this->logger->info('Making move', ['game_id' => $id, 'player' => $player, 'choice' => $choice]);

        try {
            $game = $this->gameService->makeMove($id, (int) $player, (string) $choice);
            $this->logger->info('Move made', ['game_id' => $id, 'status' => $game->status, 'winner' => $game->winner]);
            return $this->json($game->toArray());
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('Invalid move', ['game_id' => $id, 'error' => $e->getMessage()]);
            return $this->json(['error' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            $status = str_contains($e->getMessage(), 'not found') ? 404 : 500;
            $this->logger->error('Move failed', ['game_id' => $id, 'http_status' => $status, 'error' => $e->getMessage()]);
            $message = $status === 404 ? 'Game not found.' : 'Unexpected error.';
            return $this->json(['error' => $message], $status);
        }
    }
}
