<div>
    <div class="row">
        @foreach($customQuestions as $customQuestion)
            <div class="col-sm-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{route('api.modes.custom_get')}}?room={{$customQuestion->room}}" target="_blank">
                                {{ $customQuestion->room }}
                            </a>
                        </h5>
                        <button class="btn btn-primary" wire:click.prevent="selectRoom('{{$customQuestion->room}}')"
                                data-toggle="modal" data-target="#showDetail">
                            Show Detail
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div wire:ignore.self class="modal fade" id="showDetail" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="exampleModalLabel">{{ $selectedRoom ? $selectedRoom->first()->room : '' }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul>
                        @foreach($selectedRoom as $selectRoomItem)
                            <li>
                                ({{$selectRoomItem->alphabet}}) {{ $selectRoomItem->question }}
                                <br> {{ $selectRoomItem->answer }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click.prevent="closeRoom()" class="btn btn-secondary"
                            data-dismiss="modal">
                        Close
                    </button>

                </div>
            </div>
        </div>
    </div>
