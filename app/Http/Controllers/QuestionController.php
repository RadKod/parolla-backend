<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class QuestionController extends Controller
{
    /**
     * Retrieve questions resource view.
     *
     * @return View
     */
    public function index(): View
    {
        return view('question.index');
    }
}
