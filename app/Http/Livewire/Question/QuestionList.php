<?php

namespace App\Http\Livewire\Question;

use App\Models\Character;
use App\Models\Question;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionList extends Component
{
    use WithPagination;

    /**
     * Define pagination theme.
     *
     * @var string pagination theme name
     */
    protected string $paginationTheme = 'bootstrap';

    /**
     * @var string|null search keyword
     */
    public ?string $searchTerm = null;

    /**
     * @var string|null filter for specific character
     */
    public ?string $filterCharacter = null;

    /**
     * @var int associated question id for deleting process
     */
    public int $deleteQuestionId;

    // TODO: Use DTO for question store

    /**
     * @var int|null question id for editing
     */
    public ?int $questionId = null;

    /**
     * @var string|null question text
     */
    public ?string $questionString = null;

    /**
     * @var string|null question character/letter
     */
    public ?string $questionCharacter = null;

    /**
     * @var string|null question answer
     */
    public ?string $questionAnswer = null;

    /**
     * Renders component
     *
     * @return View
     */
    public function render(): View
    {
        /** @var Builder $questions */
        $questions = Question::query();

        if ($this->searchTerm) {
            $questions = $questions->search($this->searchTerm);
        }

        if ($this->filterCharacter) {
            $questions = $questions->forCharacter($this->filterCharacter);
        }

        $characters = Character::all();
        $questions = $questions->paginate(10);

        return view('question.question-list', compact('questions', 'characters'));
    }

    /**
     * Store question id for continue to delete process.
     *
     * @param int $id question id
     * @return void
     */
    public function confirmDeletion(int $id): void
    {
        $this->deleteQuestionId = $id;
    }

    /**
     * Delete question by id.
     *
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function delete(int $id): void
    {
        Question::query()->find($id)->delete();
        session()->flash('message', 'Question Deleted Successfully.');
    }

    /**
     * Filters list of questions by character.
     *
     * @param string $character
     * @return void
     */
    public function filterByCharacter(string $character): void
    {
        $character = $this->filterCharacter === $character ? null : $character;

        $this->filterCharacter = $character;
        $this->questionCharacter = $character;
    }

    /**
     * Resets form.
     *
     * @return void
     */
    public function resetForm(): void
    {
        $this->questionId = null;
        $this->questionString = null;
        $this->questionAnswer = null;
    }

    /**
     * Shows create form. Reset form if previously used.
     *
     * @return void
     * @noinspection PhpUnused
     */
    public function showCreateForm(): void
    {
        if ($this->questionId) {
            $this->resetForm();
        }
    }

    /**
     * Edit question.
     *
     * @param int $id
     * @return void
     * @noinspection PhpUnused
     */
    public function editQuestion(int $id): void
    {
        $question = Question::query()->find($id);

        $this->questionId = $question->id;
        $this->questionString = $question->question;
        $this->questionAnswer = $question->answer;
        $this->questionCharacter = $question->character;
    }

    /**
     * Creates or update question
     *
     * @return void
     */
    public function apply(): void
    {
        $this->validate([
            'questionString' => 'required|min:6',
            'questionAnswer' => 'required|unique:questions,answer,' . $this->questionId . '|min:1',
            'questionCharacter' => 'required'
        ]);

        if ($this->questionId) {
            $this->update();
        } else {
            $this->store();
        }

        $this->closeModal();
    }

    /**
     * Update question.
     *
     * @return void
     */
    public function update(): void
    {
        $question = Question::query()->find($this->questionId);
        $question->question = $this->questionString;
        $question->answer = $this->questionAnswer;
        $question->character = $this->questionCharacter;
        $question->save();

        session()->flash('message', 'Question Updated Successfully.');
    }

    /**
     * Create question.
     *
     * @return void
     */
    public function store(): void
    {
        $question = new Question();
        $question->question = $this->questionString;
        $question->answer = $this->questionAnswer;
        $question->character = $this->questionCharacter;
        $question->save();

        session()->flash('message', 'Question Created Successfully.');
    }

    /**
     * Closes modal for creating/editing question.
     *
     * @return void
     */
    public function closeModal(): void
    {
        $this->emit('closeModal');
        $this->resetForm();
    }
}
