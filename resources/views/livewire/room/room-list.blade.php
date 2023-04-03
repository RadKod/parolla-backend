<div wire:poll>
    <div class="row">
        @foreach($customQuestions as $customQuestion)
            <div class="col-sm-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <button class="btn btn-danger btn-sm" wire:click.prevent="deleteRoom('{{$customQuestion->room}}')"
                                            onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">
                                        Delete
                                    </button>
                                    {{ $customQuestion->is_public ? '(Public)' : '(Private)' }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <a href="https://www.parolla.app/room?id={{$customQuestion->room}}" target="_blank">
                                        {{ $customQuestion->title }}
                                    </a>
                                </div>
                            </div>
                        </h5>
                        <button class="btn btn-primary" wire:click.prevent="selectRoom('{{$customQuestion->room}}')"
                                data-toggle="modal" data-target="#showDetail">
                            Show Detail
                        </button>
                        <a href="{{route('api.modes.custom_get')}}?room={{$customQuestion->room}}"
                           target="_blank" class="btn btn-primary">
                            API Url
                        </a>
                        view count: {{ $customQuestion->view_count }}
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
                        id="exampleModalLabel">{{ $selectedRoom ? $selectedRoom->room : '' }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($selectedRoom)
                    <ul>
                        @foreach($selectedRoom->qa_list as $qa_item)
                            <li>
                                ({{$qa_item['character']}}) {{ $qa_item['question'] }}
                                <br> {{ $qa_item['answer'] }}
                            </li>
                        @endforeach
                    </ul>
                    @endif
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
