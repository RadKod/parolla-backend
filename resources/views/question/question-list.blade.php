<div>
    <div class="row">
        <div class="col-12">
            @if(session()->has('message'))
                <div class="alert {{session('alert') ?? 'alert-info'}} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="col-3">
            <h3>{{ __('Questions') }} ({{ $questions->total() }})</h3>
        </div>

        <div class="col-9 float-right text-right">
            <button type="button" class="btn btn-primary" wire:click="showCreateForm"
                    data-toggle="modal" data-target="#crudModal_post">
                Create Question
            </button>
        </div>
        @include('question.partials.modal')
    </div>

    <div class="row mt-2">
        <div class="col-2">
            <input type="text" wire:model="searchTerm" class="form-control" placeholder="Search question.." />
        </div>

        <div class="col-12 alphabets pt-2 pb-2">
            @foreach($characters as $character)
                <button type="button" data-toggle="tooltip" data-placement="top" title="{{ $character->questionCount }}"
                        wire:click.prevent="filterByCharacter('{{$character->character}}')"
                        class="btn {{ $filterCharacter === $character->character ? 'btn-success' : 'btn-primary' }} btn-circle btn-sm">
                    {{$character->character}}
                </button>
            @endforeach
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if(($questions->count() > 0))
                <table class="table table-striped table-hover bg-white">
                    <thead>
                    <tr>
                        <th style="width: 20px">ID</th>
                        <th>Letter</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Release At</th>
                        <th style="width: 150px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($questions as $question)
                        <tr>
                            <td>{{ $question->id }}</td>
                            <td>{{ $question->character }}</td>
                            <td>{{ $question->question }}</td>
                            <td>{{ $question->answer }}</td>
                            <td>
                                @if($question->release_at)
                                    {{ $question->release_at->format('d-m-Y') }}
                                    <br>
                                    next release at
                                    <br>
                                    {{ $question->release_at->addDays(15)->format('d-m-Y') }}
                                @else
                                    <span class="text-danger"
                                          wire:click.prevent="update_release_at_question('{{$question->id}}')"
                                          style="cursor:pointer;">Not set</span>
                                @endif
                            </td>
                            <td>
                                <button data-toggle="modal" data-target="#crudModal_post"
                                        wire:click="editQuestion({{ $question->id }})"
                                        class="btn btn-primary btn-sm">Edit
                                </button>
                                @if($deleteQuestionId && $deleteQuestionId === $question->id)
                                    <button wire:click="delete({{ $question->id }})" class="btn btn-danger btn-sm">
                                        Sure?
                                    </button>
                                @else
                                    <button wire:click="confirmDeletion({{ $question->id }})"
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
        <div class="col-12">
            {{ $questions->links() }}
        </div>
    </div>
</div>
