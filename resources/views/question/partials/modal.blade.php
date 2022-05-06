<div wire:ignore.self class="modal fade" id="crudModal_post" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ $questionId ? 'Update' : 'Create' }} Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" wire:model="questionId">

                    <div class="form-row">
                        <div class="col-6">
                            <label for="questionString">Question</label>
                            <input type="text" class="form-control" id="questionString" wire:model="questionString"
                                   placeholder="Question">
                            @error('questionString') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-6">
                            <label for="questionCharacter">Letter</label>
                            <select class="form-control" id="questionCharacter" wire:model="questionCharacter">
                                <option value="">Select Character</option>
                                @foreach($characters as $character)
                                    <option value="{{ $character->character }}">
                                        {{ $character->character }}
                                    </option>
                                @endforeach
                            </select>
                            @error('questionCharacter') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="questionAnswer">Answer</label>
                        <textarea wire:model="questionAnswer" id="questionAnswer" class="form-control"></textarea>
                        @error('questionAnswer') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" wire:click.prevent="resetForm()" class="btn btn-secondary" data-dismiss="modal">
                    Close & Reset
                </button>
                <button type="button" wire:click.prevent="apply()" class="btn btn-primary">Save changes
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
