@extends('layouts.app')

@push('scripts')
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        function checkResult() {
            let win = false;

            // Top Row
            if (
                $('#block-1.player-{{ $playerType }}:checked').length &&
                $('#block-2.player-{{ $playerType }}:checked').length &&
                $('#block-3.player-{{ $playerType }}:checked').length
            ) {
                win = true;
            }
            // Middle Row
            else if (
                $('#block-4.player-{{ $playerType }}:checked').length &&
                $('#block-5.player-{{ $playerType }}:checked').length &&
                $('#block-6.player-{{ $playerType }}:checked').length
            ) {
                win = true;
            }
            // Bottom Row
            else if (
                $('#block-7.player-{{ $playerType }}:checked').length &&
                $('#block-8.player-{{ $playerType }}:checked').length &&
                $('#block-9.player-{{ $playerType }}:checked').length
            ) {
                win = true;
            }
            // Left Column
            else if (
                $('#block-1.player-{{ $playerType }}:checked').length &&
                $('#block-4.player-{{ $playerType }}:checked').length &&
                $('#block-7.player-{{ $playerType }}:checked').length
            ) {
                win = true;
            }
            // Center Column
            else if (
                $('#block-2.player-{{ $playerType }}:checked').length &&
                $('#block-5.player-{{ $playerType }}:checked').length &&
                $('#block-8.player-{{ $playerType }}:checked').length
            ) {
                win = true;
            }
            // Right Column
            else if (
                $('#block-3.player-{{ $playerType }}:checked').length &&
                $('#block-6.player-{{ $playerType }}:checked').length &&
                $('#block-9.player-{{ $playerType }}:checked').length
            ) {
                win = true;
            }
            // Diagonal Left to Right
            else if (
                $('#block-1.player-{{ $playerType }}:checked').length &&
                $('#block-5.player-{{ $playerType }}:checked').length &&
                $('#block-9.player-{{ $playerType }}:checked').length
            ) {
                win = true;
            }
            // Diagonal Right to Left
            else if (
                $('#block-3.player-{{ $playerType }}:checked').length &&
                $('#block-5.player-{{ $playerType }}:checked').length &&
                $('#block-7.player-{{ $playerType }}:checked').length
            ) {
                win = true;
            }

            if (!win) {
                if ($('input[type=radio]:checked').length === 9) {
                    return 'tie';
                }
            } else {
                return 'win';
            }

            return false;
        }

        let pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: 'ap1',
        });

        let gamePlayChannel = pusher.subscribe('game-channel-{{ $id }}-{{ $otherPlayerId }}');
        let gameOverChannel = pusher.subscribe('game-over-channel-{{ $id }}-{{ $otherPlayerId }}');

        gamePlayChannel.bind('App\\Events\\Play', function (data) {
            let tictac = $('#block-' + data.location);

            tictac.removeClass('player-{{ $playerType }}').addClass('player-' + data.type);
            tictac.attr('checked', true);

            $('input[type=radio]').removeAttr('disabled');
            $('#player_turn').html('You\'re next!');
        });

        gameOverChannel.bind('App\\Events\\GameOver', function (data) {
            let tictac = $('#block-' + data.location);

            tictac.removeClass('player-{{ $playerType }}').addClass('player-' + data.type);
            tictac.attr('checked', true);

            if (data.result === 'win') {
                $('#player_turn').html('You Lose!');
            } else {
                $('#player_turn').html('Its a tie!');
            }

            $('#btn_exit').show();
        });

        $(function () {
            $('input[type=radio]').on('click', function () {
                $('input[type=radio]').attr('disabled', true);

                let result = checkResult();

                if (!result) {
                    $('#player_turn').html('Waiting for player 2...');

                    $.ajax({
                        url: '/play/{{ $nextTurn->game_id }}',
                        method: 'post',
                        data: {
                            _token: '{{ csrf_token() }}',
                            location: $(this).val(),
                        },
                        success: function (data) {

                        }
                    });
                } else {
                    if (result === 'win') {
                        $('#player_turn').html('You Win!');
                    } else {
                        $('#player_turn').html('Its a tie!');
                    }

                    $('#btn_exit').show();

                    $.ajax({
                        url: '/game-over/{{ $nextTurn->game_id }}',
                        method: 'post',
                        data: {
                            location: $(this).val(),
                            _token: '{{ csrf_token() }}',
                            result: result,
                        },
                        success: function (data) {
                            //
                        }
                    });
                }
            });
        });
    </script>
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h5 class="text-center" id="player_turn">
                    @if($game->end_at)
                        Game is over!
                    @else
                        {{ $user->id === $nextTurn->player_id ? 'You\'re next!' : 'Waiting for player 2...' }}
                    @endif
                </h5>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="tic-tac-toe">
                    @foreach($locations as $index => $location)
                        <input
                            type="radio"
                            class="player-{{ $location['checked'] ? $location['type'] : $playerType }} {{ $location['class'] }}"
                            id="block-{{ $index }}"
                            value="{{ $index }}"
                            {{ $location['checked'] ? 'checked' : '' }}
                            {{ $user->id !== $nextTurn->player_id ? 'disabled' : '' }}
                            {{ $game->end_at ? 'disabled' : '' }}
                        />
                        <label for="block-{{ $index }}"></label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a id="btn_exit" href="/home" class="btn btn-primary" style="display: {{ $game->end_at ? '' : 'none' }}">Exit</a>
            </div>
        </div>
    </div>
@endsection
