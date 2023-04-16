<div wire:ignore.self class="modal fade" id="crudModal_post" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ $cr_question_id ?? '' ? 'Update' : 'Create' }} Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                @if(session()->has('message'))
                    <div class="alert {{session('alert') ?? 'alert-info'}} alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <form>
                    <input type="hidden" wire:model="question_id">

                    <div class="form-row">
                        <div class="col-6">
                            <label for="cr_question">Question</label>
                            <input type="text" class="form-control" id="cr_question"
                                   wire:model="cr_question"
                                   placeholder="Question">
                            @error('cr_question') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-6">
                            <label for="cr_alphabet_id">Letter</label>
                            <select class="form-control" id="cr_alphabet_id" wire:model="cr_alphabet_id">
                                <option value="">Select Letter</option>
                                @foreach($alphabet as $alphabet_item)
                                    <option value="{{ $alphabet_item->id }}">
                                        {{ $alphabet_item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cr_alphabet_id') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cr_answer">Answer</label>
                        <textarea wire:model="cr_answer" id="cr_answer" class="form-control"></textarea>
                        @error('cr_answer') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" wire:click.prevent="reset_form()" class="btn btn-secondary" data-dismiss="modal">
                    Close & Reset
                </button>
                <button type="button" wire:click.prevent="create_or_update()" class="btn btn-primary">Save changes
                </button>
            </div>
        </div>
    </div>

    @push('ex_scripts')
        <script>
            $(document).ready(function () {
                window.livewire.on('closeModal', () => {
                    $('#crudModal_post').modal('hide');
                });
            });
        </script>
    @endpush

</div>
