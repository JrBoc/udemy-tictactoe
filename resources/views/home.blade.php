@extends('layouts.app')

@push('scripts')
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        let pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: 'ap1',
        });

        let gamePlayChannel = pusher.subscribe('new-game-channel');

        gamePlayChannel.bind('App\\Events\\NewGame', function (data) {
            if (data.destinationUserId === {{ $user->id }}) {
                $('#from').text(data.from);
                $('#frm_new_game').attr('action', '/board/' + data.gameId);
                $('#mdl_new_game').modal('show');
            }
        });
    </script>
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="profile-picture d-flex justify-items-center">
                            <img class="rounded-circle" src="https://www.gravatar.com/avatar/{{ md5($user->email) }}?d=retro&s=200">
                        </div>
                        <div class="d-block text-center mt-2">
                            <div class="h4">{{ $user->name }}</div>
                            <div class="h6"><strong>{{ $user->score }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <form action="{{ route('home') }}" class="form-inline" method="get">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">Search</button>
                            </div>
                        </form>
                    </div>
                    <div class="list-group">
                        @foreach($users as $userItem)
                            <a href="#" class="list-group-item list-group-item-action w-100 shadow-none {{ $loop->last ? 'rounded-0' : '' }}">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div class="d-flex justify-items-start align-items-center">
                                        <img class="rounded-circle responsive" src="https://www.gravatar.com/avatar/{{ md5($userItem->email) }}?d=retro&s=75">
                                        <div class="p-4">{{ $userItem->name }}
                                            <br>
                                            <small>Score: {{ $userItem->score }}</small>
                                        </div>
                                    </div>
                                    <form action="{{ route('new-game') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $userItem->id }}">
                                        <button class="btn btn-primary">PLAY</button>
                                    </form>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="card-footer p-3">
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdl_new_game" tabindex="-1" aria-labelledby="mdl_new_game" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><span id="from"></span> invited you to a game.</p>
                </div>
                <div class="modal-footer">
                    <form action="#" id="frm_new_game" method="get">
                        @csrf
                        <button type="submit" class="btn btn-primary">Play</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
