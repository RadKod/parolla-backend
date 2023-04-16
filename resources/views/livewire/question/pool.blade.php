<div>
    @include('livewire.question.inc.modal')
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
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            total: {{ number_format($total, 0, ',', '.') }}
        </div>
        <div>
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-primary"
                        wire:click="firstPage"
                        wire:loading.attr="disabled"
                        wire:target="firstPage"
                        {{ $page <= 1 ? 'disabled' : '' }}>
                    First
                </button>
                <button type="button" class="btn btn-primary"
                        wire:click="prevPage"
                        wire:loading.attr="disabled"
                        wire:target="prevPage"
                        {{ $page <= 1 ? 'disabled' : '' }}>
                    Previous
                </button>
                <button type="button" class="btn btn-outline-primary active disabled">
                    {{ $page }} ({{ $selectedLetter }})
                </button>
                <button type="button" class="btn btn-primary"
                        wire:click="nextPage"
                        wire:loading.attr="disabled"
                        wire:target="nextPage"
                        {{ $page >= $lastPage ? 'disabled' : '' }}>
                    Next
                </button>
                <button type="button" class="btn btn-primary"
                        wire:click="lastPage"
                        wire:loading.attr="disabled"
                        wire:target="lastPage"
                        {{ $page >= $lastPage ? 'disabled' : '' }}>
                    Last
                </button>
            </div>
        </div>
        <div>
            <select wire:model="selectedLetter" class="form-control">
                <option value="all">all</option>
                @foreach($alphabet as $letter)
                    <option value="{{ Transliterator::create('tr-lower')->transliterate($letter->name) }}"
                        {{ $selectedLetter === $letter->name ? 'selected' : '' }}>
                        {{ $letter->name }}
                    </option>
                @endforeach
            </select>
            <select wire:model="perPage" class="form-control">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    @foreach($questions as $question_key => $question)
        <div class="card mb-2">
            <div class="card-body">
                <h5 class="card-title">
                    <span class="badge badge-secondary">{{$question['letter']}}</span>
                    @foreach($question['question'] as $key => $value)
                        <span class="badge badge-primary" role="button" wire:click="open_create_form({{ $question_key }}, {{$key}})"
                              data-toggle="modal" data-target="#crudModal_post">
                            {{$key+1}}: {{ $value }}
                        </span>
                    @endforeach
                </h5>
                <p class="card-text">
                    <span class="badge badge-primary">{{ $question['answer'] }}</span>
                </p>
            </div>
            <div class="card-footer">
                <a href="{{ $question['url'] }}" target="_blank">{{ $question['url'] }}</a>
            </div>
        </div>
    @endforeach

</div>
