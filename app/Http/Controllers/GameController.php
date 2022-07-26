<?php

namespace App\Http\Controllers;

use App\Events\GameOver;
use App\Events\Play;
use App\Models\Game;
use App\Models\Turn;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function board(Request $request, $id)
    {
        $user = $request->user();

        $players = Turn::where('game_id', $id)->select(['player_id', 'type'])->distinct()->get();
        $playerType = $user->id == $players[0]->player_id ? $players[0]->type : $players[1]->type;
        $otherPlayerId = $user->id == $players[0]->player_id ? $players[1]->player_id : $players[0]->player_id;

        $pastTurns = Turn::where('game_id', $id)->whereNotNull('location')->orderBy('id')->get();
        $nextTurn = Turn::where('game_id', $id)->whereNull('location')->orderBy('id')->first();

        $locations = [
            1 => [
                'class' => 'top left',
                'checked' => false,
                'type' => '',
            ],
            2 => [
                'class' => 'top middle',
                'checked' => false,
                'type' => '',
            ],
            3 => [
                'class' => 'top right',
                'checked' => false,
                'type' => '',
            ],
            4 => [
                'class' => 'center left',
                'checked' => false,
                'type' => '',
            ],
            5 => [
                'class' => 'center middle',
                'checked' => false,
                'type' => '',
            ],
            6 => [
                'class' => 'center right',
                'checked' => false,
                'type' => '',
            ],
            7 => [
                'class' => 'bottom left',
                'checked' => false,
                'type' => '',
            ],
            8 => [
                'class' => 'bottom middle',
                'checked' => false,
                'type' => '',
            ],
            9 => [
                'class' => 'bottom right',
                'checked' => false,
                'type' => '',
            ],
        ];

        foreach ($pastTurns as $pastTurn) {
            $locations[$pastTurn->location]['checked'] = true;
            $locations[$pastTurn->location]['type'] = $pastTurn->type;
        }

        return view('board', [
            'user' => $user,
            'id' => $id,
            'locations' => $locations,
            'playerType' => $playerType,
            'otherPlayerId' => $otherPlayerId,
            'nextTurn' => $nextTurn,
            'game' => Game::find($id),
        ]);
    }

    public function play(Request $request, $id)
    {
        $location = $request->input('location');

        $turn = Turn::where('game_id', $id)->whereNull('location')->orderBy('id')->first();

        $turn->location = $location;
        $turn->save();

        event(new Play($id, $turn->type, $location, $turn->player_id));

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function gameOver(Request $request, $id)
    {
        $location = $request->input('location');

        $turn = Turn::where('game_id', $id)->whereNull('location')->orderBy('id')->first();

        $turn->location = $location;
        $turn->save();

        if ($request->input('result') === 'win') {
            auth()->user()->increment('score');
        }

        Game::find($id)->update([
            'winner_id' => auth()->id(),
            'end_at' => now(),
        ]);

        event(new GameOver($id, auth()->id(), $request->input('result'), $location, $turn->type));

        return response()->json([
            'status' => 'success',
        ]);
    }
}
