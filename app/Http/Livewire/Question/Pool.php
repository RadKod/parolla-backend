<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Http\Livewire\Question;

use App\Models\Alphabet;
use App\Models\Question;
use JsonException;
use Livewire\Component;
use Transliterator;

class Pool extends Component
{
    protected $questionListUrl = 'https://raw.githubusercontent.com/apo-bozdag/kelimeler.net-bot/main/questions.json';
    public $page = 1;
    public $perPage = 10;
    public $total = 0;
    public $lastPage = 0;
    public $questions = [];
    public $alphabet = [];
    public $selectedLetter = 'all';

    /**
     * Update & Create Form Data
     */
    public $cr_question;
    public $cr_alphabet_id;
    public $cr_answer;

    /**
     * @throws JsonException
     */
    public function render()
    {
        $this->alphabet = Alphabet::query()->get();
        $getQuestions = $this->fetchQuestions();
        $this->questions = collect($getQuestions);

        if ($this->selectedLetter !== 'all') {
            $this->questions = $this->questions->where('letter', $this->selectedLetter);
        }

        $this->total = count($this->questions);
        $this->lastPage = ceil($this->total / $this->perPage);
        $this->questions = $this->questions->forPage($this->page, $this->perPage);

        return view('livewire.question.pool');
    }

    /**
     * @throws JsonException
     */
    public function fetchQuestions()
    {
        return json_decode(file_get_contents($this->questionListUrl), true, 512, JSON_THROW_ON_ERROR);
    }

    public function nextPage()
    {
        $this->page++;
    }

    public function prevPage()
    {
        $this->page--;
    }

    public function firstPage()
    {
        $this->page = 1;
    }

    public function lastPage()
    {
        $this->page = $this->lastPage;
    }

    public function goToPage($page)
    {
        $this->page = $page;
    }

    public function updatedPerPage()
    {
        $this->page = 1;
    }

    public function updatedSelectedLetter()
    {
        $this->page = 1;
        $this->selectedLetter = $this->selectedLetter;
    }

    public function reset_form()
    {
        $this->cr_question = '';
        $this->cr_alphabet_id = '';
        $this->cr_answer = '';
    }

    public function open_create_form($question_index, $question_order)
    {
        $letter = Transliterator::create('tr-upper')->transliterate($this->questions[$question_index]['letter']);
        $alphabet_id_find = Alphabet::query()->where('name', $letter)->first();
        $this->reset_form();
        $this->cr_question = $this->questions[$question_index]['question'][$question_order];
        $this->cr_alphabet_id = $alphabet_id_find->id;
        $this->cr_answer = $this->questions[$question_index]['answer'];
    }

    public function create_or_update()
    {
        $this->validate([
            'cr_question' => 'required',
            'cr_alphabet_id' => 'required',
            'cr_answer' => 'required',
        ]);

        # has answer
        $has_answer = Question::query()->where('answer', 'like', '%' . $this->cr_answer . '%')->first();
        if ($has_answer) {
            $this->emit('closeModal');
            session()->flash('message', 'Bu cevap zaten mevcut.');
            return;
        }

        $question = Question::query()->where('question', $this->cr_question)->first();
        if ($question) {
            $question->alphabet_id = $this->cr_alphabet_id;
            $question->answer = $this->cr_answer;
            $question->save();
        } else {
            Question::query()->create([
                'alphabet_id' => $this->cr_alphabet_id,
                'question' => $this->cr_question,
                'answer' => $this->cr_answer,
            ]);
        }
        $this->reset_form();
        $this->emit('closeModal');
        session()->flash('message', 'Soru başarıyla eklendi.');
    }
}
