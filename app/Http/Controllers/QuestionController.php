<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('question.index');
    }

    public function rooms()
    {
        return view('room.index');
    }

    public function pools()
    {
        return view('question.pools');
    }
}
