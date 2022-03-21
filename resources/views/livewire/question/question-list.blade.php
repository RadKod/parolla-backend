<div>
    <div class="row">
        <div class="col-3">
            <h3>{{ __('Questions') }} ({{ $questions->total() }})</h3>
        </div>

        <div class="col-9 float-right text-right">
            <button type="button" class="btn btn-primary" wire:click="open_create_form"
                    data-toggle="modal" data-target="#crudModal_post">
                Create Question
            </button>
        </div>
        @include('livewire.question.inc.modal')
        <div class="col-12">
            @if(session()->has('message'))
                <div class="alert {{session('alert') ?? 'alert-info'}}" style="margin-top:30px;">
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-2">
            <input type="text" wire:model="search_term" class="form-control"
                   placeholder="Search question.."
            />
        </div>

        <div class="col-12 alphabets pt-2 pb-2">
            @foreach($alphabet as $alphabet_item)
                <button type="button" data-toggle="tooltip" data-placement="top"
                        title="{{ $alphabet_item->questions->count() }}"
                        wire:click.prevent="filter_by_alphabet('{{$alphabet_item->id}}')"
                        class="btn {{$filter_alphabet_id === $alphabet_item->id ? 'btn-success' : 'btn-primary'}} btn-circle btn-sm">
                    {{$alphabet_item->name}}
                </button>
            @endforeach
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card p-0 m-0">
                @if(($questions->count() > 0))
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th style="width: 20px">ID</th>
                            <th>Letter</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th style="width: 150px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($questions as $question)
                            <tr>
                                <td>{{ $question->id }}</td>
                                <td>{{ $question->alphabet->name }}</td>
                                <td>{{ $question->question }}</td>
                                <td>{{ $question->answer }}</td>
                                <td>
                                    <button data-toggle="modal" data-target="#crudModal_post"
                                            wire:click="edit({{ $question->id }})"
                                            class="btn btn-primary btn-sm">Edit
                                    </button>
                                    @if($confirming_delete_id && $confirming_delete_id === $question->id)
                                        <button wire:click="delete({{ $question->id }})" class="btn btn-danger btn-sm">
                                            Sure?
                                        </button>
                                    @else
                                        <button wire:click="confirm_delete({{ $question->id }})"
                                                class="btn btn-secondary btn-sm">
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info">
                        No questions found.
                    </div>
                @endif
            </div>
        </div>
        <div class="col-12 mt-2">
            {{ $questions->links() }}
        </div>
    </div>
</div>
