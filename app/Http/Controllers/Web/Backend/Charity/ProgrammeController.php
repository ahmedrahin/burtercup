<?php

namespace App\Http\Controllers\Web\Backend\Charity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Charity\Programme;

class ProgrammeController extends Controller
{
    public function create()
    {
        return view('backend.layouts.charity.programme.create');
    }

}
