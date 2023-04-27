<?php

namespace App\Http\Livewire\Question;

use App\Models\Alphabet;
use App\Models\Question;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /**
     * Filter Form Data
     */
    public $release_at;
    public $not_matched_filter_for_letter_answer;
    public $search_term;
    public $filter_alphabet_id;
    public $confirming_delete_id;

    /**
     * Update & Create Form Data
     */
    public $cr_question_id;
    public $cr_question;
    public $cr_alphabet_id;
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
        $alphabet = Alphabet::query()
            ->with(['questions', 'releasesQuestions'])
            ->get();

        $questions = Question::query()
            ->with('alphabet')
            ->search($this->search_term)
            ->alphabet($this->filter_alphabet_id)
            ->release($this->release_at)
            ->notMatched($this->not_matched_filter_for_letter_answer)
            ->paginate(10);
        return view('livewire.question.question-list', compact(
            'questions', 'alphabet'
        ));
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
     * @param int $id
     * @return void
     */
    public function filter_by_alphabet(int $id): void
    {
        $this->filter_alphabet_id = $this->filter_alphabet_id === $id ? '' : $id;
        $this->cr_alphabet_id = $this->cr_alphabet_id === $id ? '' : $id;
    }

    /**
     * @return void
     */
    public function update_release_at_question($question_id): void
    {
        $question = Question::query()->find($question_id);
        $question->release_at = $question->release_at ? null : now();
        $question->save();
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
        $this->cr_alphabet_id = $question->alphabet_id;
    }

    /**
     * @return void
     */
    public function create_or_update(): void
    {
        $this->validate([
            'cr_question' => 'required|min:6',
            'cr_answer' => 'required|unique:questions,answer,' . $this->cr_question_id . '|min:1',
            'cr_alphabet_id' => 'required'
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
        $question->alphabet_id = $this->cr_alphabet_id;
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
        $question->alphabet_id = $this->cr_alphabet_id;
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
