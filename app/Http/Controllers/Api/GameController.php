<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;


class GameController extends Controller
{
    public function createGame(Request $request)
    {
        $game = Game::create(['status' => 'waiting']);
        return response()->json($game);
    }

    public function joinGame(Request $request)
    {
        $request->validate([
            'game_id' => 'required|integer|exists:games,id',
        ]);


        $game = Game::find($request->game_id);
        // dd('game', $game);
        $player = Player::create(['game_id' => $game->id, 'user_id' => auth()->id()]);
        return response()->json($player);
    }




    public function startGame(Request $request)
    {
        $game = Game::find($request->game_id);
        $players = $game->players;

        $deck = $this->shuffleDeck();
        foreach ($players as $player) {
            $hand = array_splice($deck, 0, 3);
            $player->hand = json_encode($hand);
            $player->save();
        }

        $game->status = 'ongoing';
        $game->save();

        return response()->json($game->players);
    }


    public function placeBet(Request $request)
    {
        $player = Player::find($request->player_id);
        $player->bet = $request->bet_amount;
        $player->balance -= $request->bet_amount;
        $player->save();

        return response()->json($player);
    }


    public function checkWinner(Request $request)
    {
        $game = Game::find($request->game_id);
        $players = $game->players;

        $winner = $this->determineWinner($players);
        $game->status = 'finished';
        $game->save();

        return response()->json($winner);
    }

    private function determineWinner($players)
    {
        $handRankings = [];

        // Rank each player's hand
        foreach ($players as $player) {
            $hand = $player->hand;
            $handRankings[] = [
                'player' => $player,
                'rank' => $this->rankHand($hand),
            ];
        }

        // Sort players based on hand ranking
        usort($handRankings, function ($a, $b) {
            return $b['rank']['rank'] <=> $a['rank']['rank'] ?: $b['rank']['high_card'] <=> $a['rank']['high_card'];
        });

        // Player with the best hand
        return $handRankings[0]['player'];
    }

    private function rankHand($hand)
    {
        $ranks = $this->getRanks($hand);
        $suits = $this->getSuits($hand);

        // Sort ranks in descending order for easy comparison
        rsort($ranks);

        if ($this->isTrail($ranks)) {
            return ['rank' => 6, 'high_card' => $ranks[0]]; // Trail (Three of a Kind)
        }
        if ($this->isPureSequence($ranks, $suits)) {
            return ['rank' => 5, 'high_card' => $ranks[0]]; // Pure Sequence (Straight Flush)
        }
        if ($this->isSequence($ranks)) {
            return ['rank' => 4, 'high_card' => $ranks[0]]; // Sequence (Straight)
        }
        if ($this->isColor($suits)) {
            return ['rank' => 3, 'high_card' => $ranks[0]]; // Color (Flush)
        }
        if ($this->isPair($ranks)) {
            return ['rank' => 2, 'high_card' => $ranks[0]]; // Pair (Two of a Kind)
        }

        return ['rank' => 1, 'high_card' => $ranks[0]]; // High Card
    }

    private function getRanks($hand)
    {
        return array_map(function ($card) {
            return $this->cardValue($card);
        }, $hand);
    }

    private function getSuits($hand)
    {
        return array_map(function ($card) {
            return explode(' ', $card)[2]; // Assuming "Rank of Suit" format, gets Suit
        }, $hand);
    }

    private function cardValue($card)
    {
        $rank = explode(' ', $card)[0];
        $values = ['2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10, 'J' => 11, 'Q' => 12, 'K' => 13, 'A' => 14];
        return $values[$rank];
    }

    private function isTrail($ranks)
    {
        return count(array_unique($ranks)) === 1; // All three ranks are the same
    }

    private function isPureSequence($ranks, $suits)
    {
        return $this->isSequence($ranks) && $this->isColor($suits); // Consecutive cards of the same suit
    }

    private function isSequence($ranks)
    {
        return ($ranks[0] - $ranks[1] === 1) && ($ranks[1] - $ranks[2] === 1); // Three consecutive ranks
    }

    private function isColor($suits)
    {
        return count(array_unique($suits)) === 1; // All cards have the same suit
    }

    private function isPair($ranks)
    {
        return count(array_unique($ranks)) === 2; // Two ranks are the same
    }



    private function shuffleDeck()
    {
        $suits = ['hearts', 'diamonds', 'clubs', 'spades'];
        $ranks = [2, 3, 4, 5, 6, 7, 8, 9, 10, 'J', 'Q', 'K', 'A'];
        $deck = [];

        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $deck[] = "$rank of $suit";
            }
        }

        shuffle($deck);
        return $deck;
    }
}
