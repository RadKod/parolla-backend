<?php

namespace App\Http\Livewire\Question;

use App\Models\Character;
use App\Models\Question;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /**
     * Filter Form Data
     */
    public $search_term;
    public $filter_character;
    public $confirming_delete_id;

    /**
     * Update & Create Form Data
     */
    public $cr_question_id;
    public $cr_question;
    public $cr_character;
    public $cr_answer;

    /**
     * @return void
     */
    public function mount(): void
    {
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        /** @var Builder $questions */
        $questions = Question::query()
            ->search($this->search_term);

        if ($this->filter_character) {
            $questions = $questions->forCharacter($this->filter_character);
        }

        $alphabet = Character::all();
        $questions = $questions->paginate(10);

        return view('livewire.question.question-list', compact('questions', 'alphabet'));
    }

    /**
     * @param int $id
     * @return void
     */
    public function confirm_delete(int $id): void
    {
        $this->confirming_delete_id = $id;
    }

    /**
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function delete(int $id): void
    {
        $question = Question::query()->find($id);
        $question->delete();
        session()->flash('message', 'Question Deleted Successfully.');
    }

    /**
     * @param string $character
     * @return void
     */
    public function filter_by_alphabet(string $character): void
    {
        $this->filter_character = $this->filter_character === $character ? '' : $character;
        $this->cr_character = $this->cr_character === $character ? '' : $character;
    }

    /**
     * @return void
     */
    public function reset_form(): void
    {
        $this->cr_question_id = '';
        $this->cr_question = '';
        $this->cr_answer = '';
    }

    /**
     * @return void
     */
    public function open_create_form(): void
    {
        if ($this->cr_question_id) {
            $this->reset_form();
        }
    }

    /**
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $question = Question::query()->find($id);
        $this->cr_question_id = $question->id;
        $this->cr_question = $question->question;
        $this->cr_answer = $question->answer;
        $this->cr_character = $question->character;
    }

    /**
     * @return void
     */
    public function create_or_update(): void
    {
        $this->validate([
            'cr_question' => 'required|min:6',
            'cr_answer' => 'required|unique:questions,answer,' . $this->cr_question_id . '|min:1',
            'cr_character' => 'required'
        ]);
        if ($this->cr_question_id) {
            $this->update();
        } else {
            $this->store();
        }
        $this->close_modal();
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $question = Question::query()->find($this->cr_question_id);
        $question->question = $this->cr_question;
        $question->answer = $this->cr_answer;
        $question->character = $this->cr_character;
        $question->save();
        session()->flash('message', 'Question Updated Successfully.');
    }

    /**
     * @return void
     */
    public function store(): void
    {
        $question = new Question();
        $question->question = $this->cr_question;
        $question->answer = $this->cr_answer;
        $question->character = $this->cr_character;
        $question->save();
        session()->flash('message', 'Question Created Successfully.');
    }

    /**
     * @return void
     */
    public function close_modal(): void
    {
        $this->emit('closeModal');
        $this->reset_form();
    }
}
