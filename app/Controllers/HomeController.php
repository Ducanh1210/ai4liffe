<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\MajorModel;

class HomeController extends Controller
{
    public function index(): void
    {
        $majors = (new MajorModel())->all();
        $this->view('home', [
            'majors' => $majors,
        ]);
    }
}


