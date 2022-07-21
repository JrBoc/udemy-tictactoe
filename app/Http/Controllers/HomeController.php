<?php

namespace App\Http\Controllers;

use App\Events\NewGame;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        return view('home', [
            'user' => $user = auth()->user(),
            'users' => User::query()
                ->where('id', '!=', $user->id)->when(request('search'), function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->get('search')}%");
                })->paginate(15),
        ]);
    }

    public function newGame(Request $request)
    {
        $game = Game::create();

        for ($i = 1; $i <= 9; $i++) {
            $game->turns()->create([
                'id' => $i,
                'player_id' => $i % 2 ? auth()->id() : $request->input('user_id'),
                'type' => $i % 2 ? 'x' : 'o',
            ]);
        }

        event(new NewGame($request->input('user_id'), $game->id, auth()->user()->name));

        return redirect("/board/{$game->id}/");
    }
}
